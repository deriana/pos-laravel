document.addEventListener("DOMContentLoaded", function () {
    const paymentDateInput = document.getElementById("payment_date");

    // Mendapatkan tanggal saat ini dalam format YYYY-MM-DD
    const today = new Date().toISOString().split("T")[0];

    // Menetapkan nilai input dengan tanggal saat ini
    paymentDateInput.value = today;
    const cartProductsContainer = document.getElementById("cart-products");
    const addToCartButtons = document.querySelectorAll(".add-to-cart");
    const grandTotalElement = document.getElementById("grand-total");
    const changeElement = document.getElementById("change");
    const discountInput = document.getElementById("discount");

    // Fungsi untuk menghitung total
    function calculateTotal() {
        let grandTotal = 0;
        const amountPaid = 0;

        // Menghitung subtotal untuk setiap produk
        const cartItems = document.querySelectorAll(".cart-item");
        cartItems.forEach((item) => {
            const quantity =
                parseFloat(item.querySelector(".quantity").value) || 1;
            const sellingPrice =
                parseFloat(item.querySelector(".selling-price").value) || 0;
            const subtotal = quantity * sellingPrice;
            grandTotal += subtotal;

            // Menampilkan subtotal di setiap produk
            item.querySelector(".subtotal").textContent = `Rp ${subtotal
                .toFixed(2)
                .replace(/\d(?=(\d{3})+\.)/g, "$&,")}`;
        });

        // Mengambil diskon dan menghitung total setelah diskon
        let discountPercentage = parseFloat(discountInput.value) || 0;
        let discountAmount = (discountPercentage / 100) * grandTotal;
        let totalAfterDiscount = grandTotal - discountAmount;

        // Menghitung pajak 11% dari total setelah diskon
        let taxAmount = totalAfterDiscount * 0.11;
        let totalWithTax = totalAfterDiscount + taxAmount;

        // Menampilkan Grand Total setelah diskon dan pajak
        grandTotalElement.innerHTML = `
            Rp ${totalWithTax.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,")}
            <small>(Termasuk Pajak 11%: Rp ${taxAmount
                .toFixed(2)
                .replace(/\d(?=(\d{3})+\.)/g, "$&,")})</small>
        `;

        // Menghitung dan menampilkan Kembalian
        const change = amountPaid - totalWithTax;
        changeElement.textContent = `Rp ${change < 0
            ? 0
            : change.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,")
            }`;
    }

    // Event Listener untuk tombol tambah ke keranjang
    addToCartButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const productId = this.getAttribute("data-id");
            const productName = this.getAttribute("data-name");
            const productPrice = this.getAttribute("data-price");
            const productImage = this.getAttribute("data-image");

            let existingCartItem = null;
            const cartItems = document.querySelectorAll(".cart-item");
            cartItems.forEach((item) => {
                if (
                    item.querySelector('input[name^="products"]')?.value ===
                    productId
                ) {
                    existingCartItem = item;
                }
            });

            // Membuat elemen baru untuk produk yang ditambahkan ke keranjang
            if (existingCartItem) {
                // Jika produk sudah ada, tambahkan quantity-nya
                const quantityInput =
                    existingCartItem.querySelector(".quantity");
                let currentQuantity = parseInt(quantityInput.value) || 1;
                quantityInput.value = currentQuantity + 1;

                // Update harga jual jika diperlukan
                const priceInput =
                    existingCartItem.querySelector(".selling-price");
                priceInput.value = productPrice;

                // Update total setelah quantity berubah
                calculateTotal();
            } else {
                // Jika produk belum ada, tambahkan produk baru ke keranjang
                const cartItem = document.createElement("div");
                cartItem.classList.add("cart-item", "mb-3");
                cartItem.innerHTML = `
        <div class="col-12">
            <div class="card cart-product-card w-100" style="height: 450px;">
                <img src="http://localhost:8000/storage/images/${productImage}" class="card-img-top" alt="${productName}" style="max-height: 150px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">${productName}</h5>

                    <input type="hidden" name="products[${cartProductsContainer.children.length}][id]" value="${productId}">
                    

                    <p class="card-text">Harga: Rp ${productPrice}</p>
                    <div class="d-flex flex-column justify-content-between align-items-start">
                        <div class="mb-3 w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-sm btn-secondary quantity-minus">-</button>
                                <input type="number" 
                                    name="products[${cartProductsContainer.children.length}][quantity]" 
                                    class="form-control form-control-sm quantity text-center" 
                                    value="1" 
                                    min="1" 
                                    readonly 
                                    required 
                                    oninput="calculateTotal()">
                                <button type="button" class="btn btn-sm btn-secondary quantity-plus">+</button>
                            </div>
                        </div>
                        <div class="mb-3 w-100">
                            <label class="form-label">Harga Produk</label>
                            <input type="number" name="products[${cartProductsContainer.children.length}][price]" class="form-control selling-price" value="${productPrice}" required oninput="calculateTotal()">
                        </div>
                        <div class="mb-3 w-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="subtotal">Rp 0</span>
                                <button type="button" class="btn btn-danger btn-sm remove-product">Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

                cartProductsContainer.appendChild(cartItem);

                // Event listener untuk menghapus produk dari keranjang
                cartItem
                    .querySelector(".remove-product")
                    .addEventListener("click", function () {
                        cartItem.remove();
                        calculateTotal(); // Recalculate total saat produk dihapus
                    });

                // Event listener untuk tombol tambah dan kurangi quantity
                const quantityInput = cartItem.querySelector(".quantity");
                const quantityPlusButton = cartItem.querySelector(".quantity-plus");
                const quantityMinusButton = cartItem.querySelector(".quantity-minus");

                quantityPlusButton.addEventListener("click", function () {
                    quantityInput.value = parseInt(quantityInput.value) + 1;
                    calculateTotal();
                });

                quantityMinusButton.addEventListener("click", function () {
                    if (parseInt(quantityInput.value) > 1) {
                        quantityInput.value = parseInt(quantityInput.value) - 1;
                    }
                    calculateTotal();
                });

                calculateTotal(); // Recalculate total setiap kali produk ditambahkan
            }
        });
    });

    document
        .getElementById("supplier_option")
        .addEventListener("change", function () {
            // Ambil nilai dari opsi yang dipilih
            var selectedOption = this.value;

            // Sembunyikan semua elemen terlebih dahulu
            document.getElementById("supplier-select-container").style.display =
                "none";
            document.getElementById("supplier-input-container").style.display =
                "none";
            document
                .querySelector('input[name="supplier[name]"]')
                .removeAttribute("required");
            document
                .querySelector('input[name="supplier[phone_number]"]')
                .removeAttribute("required");
            document
                .querySelector('input[name="supplier[email]"]')
                .removeAttribute("required");
            document
                .querySelector('textarea[name="supplier[address]"]')
                .removeAttribute("required");
            // Periksa nilai yang dipilih dan sesuaikan tampilan
            if (selectedOption === "input") {
                // Tampilkan form input manual dan sembunyikan dropdown supplier
                document.getElementById(
                    "supplier-input-container"
                ).style.display = "block";
                document
                    .querySelector('input[name="supplier[name]"]')
                    .setAttribute("required", "true");
                document
                    .querySelector('input[name="supplier[phone_number]"]')
                    .setAttribute("required", "true");
                document
                    .querySelector('input[name="supplier[email]"]')
                    .setAttribute("required", "true");
                document
                    .querySelector('textarea[name="supplier[address]"]')
                    .setAttribute("required", "true");
            } else if (selectedOption === "select") {
                document
                    .querySelector('input[name="supplier[name]"]')
                    .removeAttribute("required");
                document
                    .querySelector('input[name="supplier[phone_number]"]')
                    .removeAttribute("required");
                document
                    .querySelector('input[name="supplier[email]"]')
                    .removeAttribute("required");
                document
                    .querySelector('textarea[name="supplier[address]"]')
                    .removeAttribute("required");
                document.getElementById(
                    "supplier-select-container"
                ).style.display = "block";
            } else if (selectedOption === "select-opsi") {
                document.getElementById(
                    "supplier-select-container"
                ).style.display = "none";
                document.getElementById(
                    "supplier-input-container"
                ).style.display = "none";
            }
        });

    // Filter Produk
    const searchInput = document.getElementById("search_product");
    const categoryFilter = document.getElementById("filter_category");
    const productCards = document.querySelectorAll(".product-card");

    function filterProducts() {
        const searchValue = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;

        productCards.forEach((card) => {
            const productName = card.getAttribute("data-name");
            const productCategory = card.getAttribute("data-category");

            const matchesSearch = productName.includes(searchValue);
            const matchesCategory =
                selectedCategory === "" || selectedCategory === productCategory;

            if (matchesSearch && matchesCategory) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    }

    searchInput.addEventListener("input", filterProducts);
    categoryFilter.addEventListener("change", filterProducts);

    // Event listener untuk diskon dan jumlah dibayar
    discountInput.addEventListener("input", calculateTotal);
    amountPaidInput.addEventListener("input", calculateTotal);

    calculateTotal(); // Initial total calculation saat halaman dimuat
});
