@extends('layout.app')

@section('content')
    <style>
    </style>

    <div class="container-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Products</h1>
            <div class="d-flex align-items-center">
                <a href="{{ route('products.create') }}" class="btn btn-primary me-3">+ Create Product</a>
                <button id="toggleMenuBtn" class="btn btn-outline-secondary" data-bs-toggle="offcanvas"
                    data-bs-target="#cartSidebar">
                    <i class='bx bx-cart-alt'></i> Cart <span id="cartCount" class="badge bg-secondary">0</span>
                </button>
            </div>
        </div>


        <!-- Search + Filter -->
        <form class="row g-3 align-items-center mb-4" action="{{ route('products.index') }}" method="GET">
            <div class="col-md-6">
                <div class="form-floating h-100">
                    <input type="text" class="form-control" id="floatingInput" placeholder="Search product"
                        name="search" value="{{ request('search') }}">
                    <label for="floatingInput">Search</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating h-100">
                    <select class="form-select" id="floatingSelect" name="category">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <label for="floatingSelect">Category</label>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-secondary w-100 h-100" type="submit">Filter</button>
            </div>

        </form>

        <!-- Product Cards -->
        <div>
            <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
                @forelse ($products as $product)
                    <div class="col">
                        <div class="card h-100 hover-effect">
                            <div class="card-img-container"
                                style="position: relative; width: 100%; padding-bottom: 56.25%; overflow: hidden;">
                                <img class="card-img-top" src="{{ url('storage/images/' . $product->product_image) }}"
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                                    alt="Product Image">
                            </div>
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-category mb-0">{{ $product->category->name }}</p>
                                </div>
                                <div class="d-flex justify-content-between w-100">
                                    <div>
                                        <div class="mb-3 d-flex">
                                            <strong>SKU:</strong>
                                            <p class="mb-0 ms-2">{{ $product->sku }}</p>
                                        </div>

                                        <div class="mb-3 d-flex">
                                            <strong>Stock:</strong>
                                            <p class="mb-0 ms-2">{{ $product->stock }}</p>
                                        </div>

                                        <div class="mb-3 d-flex">
                                            <strong>Unit:</strong>
                                            <p class="mb-0 ms-2">{{ $product->stock }}.{{ $product->unit }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="mb-3 d-flex">
                                            <strong>Purchase Price:</strong>
                                            <p class="mb-0 ms-2">Rp
                                                {{ number_format($product->purchase_price, 2, ',', '.') }}</p>
                                        </div>

                                        <div class="mb-3 d-flex">
                                            <strong>Selling Price:</strong>
                                            <p class="mb-0 ms-2">Rp
                                                {{ number_format($product->selling_price, 2, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <a href="{{ route('products.edit', $product->id) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <button class="btn btn-primary add-to-cart-btn" data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}" data-price="{{ $product->selling_price }}"
                                    data-image="{{ url('storage/images/' . $product->product_image) }}">Add to
                                    Cart</button>

                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p>No products found.</p>
                @endforelse
            </div>
        </div>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="cartSidebar" aria-labelledby="cartSidebarLabel">
            <div class="offcanvas-header">
                <h5 id="cartSidebarLabel">Your Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <!-- Cart Items -->
                <div id="cartItems" class="list-group mb-3">
                    <!-- Example of a cart item -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="product-image.jpg" alt="Product Image" class="rounded-2 me-3"
                                style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0">Product Name</h6>
                                <small class="text-muted">Rp 100,000</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-secondary btn-sm me-2"
                                onclick="updateQuantity('decrease', 'productId')">-</button>
                            <span id="quantity-productId" class="mx-2">1</span>
                            <button class="btn btn-outline-secondary btn-sm"
                                onclick="updateQuantity('increase', 'productId')">+</button>
                            <button class="btn btn-danger btn-sm ms-3" onclick="removeFromCart('productId')">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cart Total Section -->
                <!-- Cart Total Section -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <h6 class="mb-0">Total</h6>
                    <h6 id="cartTotalAmount" class="fw-bold">Rp 100,000</h6>
                </div>

                <!-- Clear Cart and Checkout Button in One Row -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button class="btn btn-outline-danger d-flex align-items-center" id="clearCartBtn">
                        <i class='bx bx-trash'></i> Clear Cart
                    </button>
                    <button class="btn btn-success" id="checkoutBtn">Proceed to Checkout</button>
                </div>
            </div>
        </div>



        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>
    <script>
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const price = this.getAttribute('data-price');
                const image = this.getAttribute('data-image');

                // Cek apakah produk sudah ada di keranjang
                const existingProduct = cart.find(item => item.id === id);
                if (existingProduct) {
                    existingProduct.qty += 1; // Tambahkan qty
                } else {
                    cart.push({
                        id,
                        name,
                        price,
                        image,
                        qty: 1
                    });
                }

                // Update keranjang di localStorage
                saveCart();

                // Update UI dan tombol "Add to Cart"
                updateCart();
                updateButtonState(this, id); // Menambahkan update tombol berdasarkan produk
            });
        });

        // Fungsi untuk memperbarui tampilan tombol "Add to Cart"
        function updateButtonState(button, productId) {
            const existingProduct = cart.find(item => item.id === productId);
            if (existingProduct) {
                button.innerText = "Sudah di Keranjang"; // Ganti teks tombol jika produk ada di keranjang
                button.disabled = true; // Optional: nonaktifkan tombol setelah produk ada di keranjang
            } else {
                button.innerText = "Add to Cart"; // Kembali ke teks awal
                button.disabled = false; // Mengaktifkan tombol jika produk tidak ada di keranjang
            }
        }

        // Fungsi untuk mengupdate quantity produk di keranjang
        function updateQuantity(action, id) {
            const product = cart.find(item => item.id === id);

            if (action === 'increase') {
                product.qty += 1; // Tambahkan jumlah
            } else if (action === 'decrease' && product.qty > 1) {
                product.qty -= 1; // Kurangi jumlah (minimal 1)
            }

            // Simpan perubahan di localStorage dan update UI
            saveCart();
            updateCart();
        }

        // Fungsi untuk menghapus produk dari keranjang
        // Fungsi untuk menghapus produk dari keranjang
        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id); // Hapus produk berdasarkan id
            saveCart(); // Simpan perubahan ke localStorage
            updateCart(); // Update UI setelah produk dihapus

            // Update status tombol "Add to Cart" setelah item dihapus
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                const productId = button.getAttribute('data-id');
                updateButtonState(button, productId); // Memastikan tombol kembali seperti semula
            });
        }


        // Update keranjang di UI
        function updateCart() {
            let cartItems = '';
            let total = 0;

            cart.forEach(item => {
                cartItems += `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="${item.image}" alt="${item.name}" class="rounded-2 me-3" style="width: 50px; height: 50px; object-fit: cover;">
                    <div>
                        <h6 class="mb-0">${item.name}</h6>
                    <small class="text-muted">
                    Rp ${parseFloat(item.price).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                    </small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary btn-sm me-2" onclick="updateQuantity('decrease', '${item.id}')">-</button>
                    <span id="quantity-${item.id}" class="mx-2">${item.qty}</span>
                    <button class="btn btn-outline-secondary btn-sm" onclick="updateQuantity('increase', '${item.id}')">+</button>
                    <button class="btn btn-danger btn-sm ms-3" onclick="removeFromCart('${item.id}')">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            </div>
        `;
                total += item.qty * parseFloat(item.price);
            });
            document.getElementById('cartItems').innerHTML = cartItems;
            document.getElementById('cartTotalAmount').innerText = total.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });
            document.getElementById('cartCount').innerText = cart.length;
            // Clear Cart Button functionality
            document.getElementById('clearCartBtn').addEventListener('click', function() {
                cart = []; // Kosongkan keranjang
                saveCart(); // Simpan perubahan ke localStorage
                updateCart(); // Update UI setelah keranjang dikosongkan

                document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                    const productId = button.getAttribute('data-id');
                    updateButtonState(button, productId); // Memastikan tombol kembali seperti semula
                });
            });

            document.getElementById('checkoutBtn').addEventListener('click', function() {
                fetch('/checkout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            cart: cart
                        })
                    })
                    .then(response => {
                        if (response.redirected) {
                            window.location.href = response.url; // Langsung redirect ke halaman sales.index
                        } else {
                            return response.json();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat checkout.');
                    });
            });
        }

        // Fungsi untuk menyimpan cart ke localStorage
        function saveCart() {
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        // Memastikan status tombol diupdate saat halaman dimuat
        window.onload = function() {
            // Perbarui UI dan status tombol berdasarkan data keranjang yang ada
            updateCart();
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                const productId = button.getAttribute('data-id');
                updateButtonState(button, productId);
            });
        }
    </script>
@endsection
