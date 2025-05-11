@extends('layout.app')

@section('content')
    <style>
        .card-img-top {
            max-width: 100%;
            max-height: 200px;
            object-fit: cover;
        }
    </style>
    <div class="container mt-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Oops!</strong> Ada beberapa masalah dengan input kamu.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2 class="mb-4">Form Pembelian</h2>
        <div class="row">
            <!-- Sisi Kiri - List Produk -->
            <div class="col-md-6">
                <div class="mb-4">
                    <label for="search_product" class="form-label fw-semibold">Cari Produk</label>
                    <input type="text" id="search_product" class="form-control shadow-sm" placeholder="Cari produk...">
                </div>

                <div class="mb-4">
                    <label for="filter_category" class="form-label fw-semibold">Filter Kategori</label>
                    <select id="filter_category" class="form-select shadow-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row row-cols-1 row-cols-md-2 g-4">
                    @foreach ($products as $product)
                        <div class="col">
                            <div class="card h-100">
                                <img src="{{ $product->product_image ? url('storage/images/' . $product->product_image) : asset('images/box-icon.jpg') }}"
                                    class="card-img-top" alt="Product {{ $product->name }}">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-category">{{ $product->category->name }}</p>
                                    <p class="card-text">Rp {{ number_format($product->purchase_price, 2, ',', '.') }}</p>
                                    <button class="btn btn-primary btn-sm add-to-cart" data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}" data-price="{{ $product->purchase_price }}"
                                        data-stock="{{ $product->stock }}" data-image="{{ $product->product_image }}">
                                        Tambah ke Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Sisi Kanan - Form Transaksi -->
            <div class="col-md-6">
                <form action="{{ route('purchases.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="supplier_option" class="form-label fw-semibold">Supplier</label>
                        <select id="supplier_option" class="form-select shadow-sm">
                            <option value="select-opsi">Pilih Opsi</option>
                            <option value="select">Pilih dari Daftar</option>
                            <option value="input">Input Manual</option>
                        </select>
                    </div>

                    <div id="supplier-select-container" style="display: none;" class="mb-4">
                        <select name="supplier_id" id="supplier_id" class="form-select shadow-sm">
                            <option value="">Pilih Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="supplier-input-container" style="display: none;" class="mb-4">
                        <input type="text" name="supplier[name]" class="form-control mb-2 shadow-sm"
                            placeholder="Nama Supplier">
                        <input type="text" name="supplier[phone_number]" class="form-control mb-2 shadow-sm"
                            placeholder="No. Telepon">
                        <input type="email" name="supplier[email]" class="form-control mb-2 shadow-sm"
                            placeholder="Email">
                        <textarea name="supplier[address]" class="form-control mb-2 shadow-sm" placeholder="Alamat" rows="3"></textarea>
                    </div>

                    <div class="mb-4">
                        <h5>Produk yang Dipilih</h5>
                        <div id="cart-products" class="cart-products-container row row-cols-1 row-cols-md-2 g-4">
                            <!-- Produk akan ditambahkan secara dinamis di sini -->
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="discount" class="form-label fw-semibold">Diskon (%)</label>
                        <input type="number" name="discount" class="form-control" id="discount" min="0"
                            max="100" step="0.01" placeholder="Masukkan Diskon (%)">
                    </div>

                    <div class="mb-4">
                        <label for="payment_method" class="form-label fw-semibold">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="credit">Kredit</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="payment_date" class="form-label fw-semibold">Tanggal Pembayaran</label>
                        <input type="date" name="payment_date" class="form-control" id="payment_date" value="now">
                    </div>

                    <div class="mb-4">
                        <label for="note" class="form-label fw-semibold">Catatan</label>
                        <textarea name="note" class="form-control" id="note" rows="3"
                            placeholder="Masukkan catatan tambahan"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="amount_paid" class="form-label fw-semibold">Jumlah Dibayar</label>
                        <input type="number" id="amount_paid" name="amount_paid" class="form-control" min="0"
                            placeholder="Masukkan Jumlah yang Dibayar" oninput="calculateTotal()">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="mb-4">
                            <h5>Grand Total</h5>
                            <p id="grand-total">Rp 0</p>
                        </div>

                        <div class="mb-4">
                            <h5>Kembalian</h5>
                            <p id="change">Rp 0</p>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Simpan Transaksi</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const paymentDateInput = document.getElementById('payment_date');

                // Mendapatkan tanggal saat ini dalam format YYYY-MM-DD
                const today = new Date().toISOString().split('T')[0];

                // Menetapkan nilai input dengan tanggal saat ini
                paymentDateInput.value = today;
                const cartProductsContainer = document.getElementById('cart-products');
                const addToCartButtons = document.querySelectorAll('.add-to-cart');
                const grandTotalElement = document.getElementById('grand-total');
                const changeElement = document.getElementById('change');
                const discountInput = document.getElementById('discount');
                const amountPaidInput = document.getElementById('amount_paid');

                // Fungsi untuk menghitung total
                function calculateTotal() {
                    let grandTotal = 0;
                    const amountPaid = parseFloat(amountPaidInput.value) || 0;

                    // Menghitung subtotal untuk setiap produk
                    const cartItems = document.querySelectorAll('.cart-item');
                    cartItems.forEach(item => {
                        const quantity = parseFloat(item.querySelector('.quantity').textContent) || 1;
                        const sellingPrice = parseFloat(item.querySelector('.selling-price').value) || 0;
                        const subtotal = quantity * sellingPrice;
                        grandTotal += subtotal;

                        // Menampilkan subtotal di setiap produk
                        item.querySelector('.subtotal').textContent =
                            `Rp ${subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
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
                        Rp ${totalWithTax.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}
                        <small>(Termasuk Pajak 11%: Rp ${taxAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')})</small>
                    `;

                    // Menghitung dan menampilkan Kembalian
                    const change = amountPaid - totalWithTax;
                    changeElement.textContent =
                        `Rp ${change < 0 ? 0 : change.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
                }

                // Event Listener untuk tombol tambah ke keranjang
                addToCartButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.getAttribute('data-id');
                        const productName = this.getAttribute('data-name');
                        const productPrice = this.getAttribute('data-price');
                        const productImage = this.getAttribute('data-image');

                        let existingCartItem = null;
                        const cartItems = document.querySelectorAll('.cart-item');
                        cartItems.forEach(item => {
                            if (item.querySelector('input[name^="products"]')?.value ===
                                productId) {
                                existingCartItem = item;
                            }
                        });

                        // Membuat elemen baru untuk produk yang ditambahkan ke keranjang
                        if (existingCartItem) {
                            // Jika produk sudah ada, tambahkan quantity-nya
                            const quantitySpan = existingCartItem.querySelector('.quantity');
                            let currentQuantity = parseInt(quantitySpan.textContent);
                            quantitySpan.textContent = currentQuantity + 1;

                            // Update harga jual jika diperlukan
                            const priceInput = existingCartItem.querySelector('.selling-price');
                            priceInput.value = productPrice;

                            // Update total setelah quantity berubah
                            calculateTotal();
                        } else {
                            // Jika produk belum ada, tambahkan produk baru ke keranjang
                            const cartItem = document.createElement('div');
                            cartItem.classList.add('cart-item', 'mb-3');
                            cartItem.innerHTML = `
                    <div class="col-12">
                        <div class="card cart-product-card w-100" style="height: 400px;">
                            <img src="{{ url('storage/images/') }}/${productImage}" class="card-img-top" alt="${productName}" style="max-height: 150px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">${productName}</h5>

                                <!-- ID produk (penting untuk validasi Laravel) -->
                                <input type="hidden" name="products[${cartProductsContainer.children.length}][id]" value="${productId}">
                                
                                <!-- Hidden input untuk quantity -->
                                <input type="hidden" name="products[${cartProductsContainer.children.length}][quantity]">

                                <p class="card-text">Harga: Rp ${productPrice}</p>
                                <div class="d-flex flex-column justify-content-between align-items-start">
                                    <div class="mb-3 w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <button type="button" class="btn btn-sm btn-secondary decrease-quantity">-</button>
                                            <span class="quantity">1</span>
                                            <button type="button" class="btn btn-sm btn-secondary increase-quantity">+</button>
                                        </div>
                                    </div>
                                    <div class="mb-3 w-100">
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
                            cartItem.querySelector('.remove-product').addEventListener('click',
                                function() {
                                    cartItem.remove();
                                    calculateTotal(); // Recalculate total saat produk dihapus
                                });

                            // Event listener untuk tombol tambah dan kurang
                            const decreaseButton = cartItem.querySelector('.decrease-quantity');
                            const increaseButton = cartItem.querySelector('.increase-quantity');
                            const quantitySpan = cartItem.querySelector('.quantity');

                            decreaseButton.addEventListener('click', function() {
                                let currentQuantity = parseInt(quantitySpan.textContent);
                                if (currentQuantity > 1) {
                                    quantitySpan.textContent = currentQuantity - 1;
                                    // Update quantity di form input
                                    cartItem.querySelector(
                                            'input[name^="products"][name$="[quantity]"]')
                                        .value = currentQuantity - 1;
                                    calculateTotal(); // Recalculate total saat quantity berubah
                                }
                            });

                            increaseButton.addEventListener('click', function() {
                                let currentQuantity = parseInt(quantitySpan.textContent);
                                quantitySpan.textContent = currentQuantity + 1;
                                // Update quantity di form input
                                cartItem.querySelector(
                                        'input[name^="products"][name$="[quantity]"]').value =
                                    currentQuantity + 1;
                                calculateTotal(); // Recalculate total saat quantity berubah
                            });


                            calculateTotal(); // Recalculate total setiap kali produk ditambahkan
                        }
                    });
                });

                document.getElementById('supplier_option').addEventListener('change', function() {
                    // Ambil nilai dari opsi yang dipilih
                    var selectedOption = this.value;

                    // Sembunyikan semua elemen terlebih dahulu
                    document.getElementById('supplier-select-container').style.display = 'none';
                    document.getElementById('supplier-input-container').style.display = 'none';

                    // Periksa nilai yang dipilih dan sesuaikan tampilan
                    if (selectedOption === 'input') {
                        // Tampilkan form input manual dan sembunyikan dropdown supplier
                        document.getElementById('supplier-input-container').style.display = 'block';
                        document.querySelector('input[name="supplier[name]"]').setAttribute('required', 'true');
                        document.querySelector('input[name="supplier[phone_number]"]').setAttribute('required',
                            'true');
                        document.querySelector('input[name="supplier[email]"]').setAttribute('required',
                            'true');
                        document.querySelector('textarea[name="supplier[address]"]').setAttribute('required',
                            'true');
                    } else if (selectedOption === 'select') {
                        document.querySelector('input[name="supplier[name]"]').removeAttribute('required');
                        document.querySelector('input[name="supplier[phone_number]"]').removeAttribute(
                            'required');
                        document.querySelector('input[name="supplier[email]"]').removeAttribute('required');
                        document.querySelector('textarea[name="supplier[address]"]').removeAttribute(
                            'required');
                        document.getElementById('supplier-select-container').style.display = 'block';
                    } else if (selectedOption === 'select-opsi') {
                        document.getElementById('supplier-select-container').style.display = 'none';
                        document.getElementById('supplier-input-container').style.display = 'none';
                    }
                });

                // Filter Produk
                const searchInput = document.getElementById('search_product');
                const categoryFilter = document.getElementById('filter_category');
                const productCards = document.querySelectorAll('.product-card');

                function filterProducts() {
                    const searchValue = searchInput.value.toLowerCase();
                    const selectedCategory = categoryFilter.value;

                    productCards.forEach(card => {
                        const productName = card.getAttribute('data-name');
                        const productCategory = card.getAttribute('data-category');

                        const matchesSearch = productName.includes(searchValue);
                        const matchesCategory = selectedCategory === "" || selectedCategory === productCategory;

                        if (matchesSearch && matchesCategory) {
                            card.style.display = "block";
                        } else {
                            card.style.display = "none";
                        }
                    });
                }

                searchInput.addEventListener('input', filterProducts);
                categoryFilter.addEventListener('change', filterProducts);

                // Event listener untuk diskon dan jumlah dibayar
                discountInput.addEventListener('input', calculateTotal);
                amountPaidInput.addEventListener('input', calculateTotal);

                calculateTotal(); // Initial total calculation saat halaman dimuat
            });
        </script>
    @endpush
@endsection
