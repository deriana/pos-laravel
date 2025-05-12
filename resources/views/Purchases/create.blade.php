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
        {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Oops!</strong> Ada beberapa masalah dengan input kamu.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

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
                                    <p class="card-stock">Stock {{ $product->stock }} {{ $product->unit }}</p>
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
                        <label for="payment_methode" class="form-label fw-semibold">Metode Pembayaran</label>
                        <select name="payment_methode" id="payment_methode" class="form-select">
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

    @push('script')
        <script src="{{ asset('js/transactions/purchase.js') }}"></script>
    @endpush
@endsection
