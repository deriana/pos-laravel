@extends('layout.app')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Form Pembelian</h2>
        <form action="{{ route('purchases.store') }}" method="POST">
            @csrf

            <!-- Pilih atau Input Supplier -->
            <div class="mb-4">
                <label for="supplier_option" class="form-label fw-semibold">Supplier</label>
                <select id="supplier_option" class="form-select shadow-sm">
                    <option value="select">Pilih dari Daftar</option>
                    <option value="input">Input Manual</option>
                </select>
            </div>

            <!-- Supplier Select -->
            <div class="mb-4" id="supplier-select-container">
                <select name="supplier_id" id="supplier_id" class="form-select shadow-sm">
                    <option value="">Pilih Supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Supplier Manual Input -->
            <div id="supplier-input-container" style="display: none;">
                <input type="text" name="supplier[name]" class="form-control mb-2 shadow-sm" placeholder="Nama Supplier" required>
                <input type="text" name="supplier[phone_number]" class="form-control mb-2 shadow-sm" placeholder="No. Telepon" required>
                <input type="email" name="supplier[email]" class="form-control mb-2 shadow-sm" placeholder="Email" required>
                <textarea name="supplier[address]" class="form-control mb-2 shadow-sm" placeholder="Alamat" rows="3" required></textarea>
            </div>

            <!-- Produk -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Pilih Produk</label>
                <div id="product-selection">
                    <div class="product-item mb-3">
                        <select name="products[0][id]" class="form-select product-id shadow-sm" required>
                            <option value="">Pilih Produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} - Rp. {{ number_format($product->purchase_price, 2) }} - Stok: {{ $product->stock }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="products[0][quantity]" class="form-control mt-2 shadow-sm quantity" placeholder="Jumlah" min="1" required>
                        <input type="number" name="products[0][price]" class="form-control mt-2 shadow-sm price" placeholder="Harga Satuan" step="any" required>
                        <input type="hidden" name="products[0][subtotal]" class="form-control mt-2 subtotal">
                        <button type="button" class="btn btn-danger mt-2 remove-product" style="display:none;">Hapus Produk</button>
                    </div>
                </div>
                <button type="button" id="add-product" class="btn btn-outline-primary mt-2">Tambah Produk</button>
            </div>

            <!-- Diskon -->
            <div class="mb-4">
                <label for="discount" class="form-label fw-semibold">Diskon (%)</label>
                <input type="number" name="discount" id="discount" class="form-control shadow-sm" step="any" min="0" max="100" value="0">
            </div>

            <!-- PPN -->
            <div class="mb-4">
                <label class="form-label fw-semibold">PPN (11%)</label>
                <input type="text" class="form-control shadow-sm" value="11%" readonly>
            </div>

            <!-- Jumlah Dibayar -->
            <div class="mb-4">
                <label for="amount_paid" class="form-label fw-semibold">Jumlah Dibayar</label>
                <input type="number" name="amount_paid" id="amount_paid" class="form-control shadow-sm" min="0" step="any">
            </div>

            <!-- Metode Pembayaran -->
            <div class="mb-4">
                <label for="payment_method" class="form-label fw-semibold">Metode Pembayaran</label>
                <select name="payment_method" id="payment_method" class="form-select shadow-sm">
                    <option value="cash">Cash</option>
                    <option value="credit">Credit</option>
                </select>
            </div>

            <!-- Tanggal -->
            <div class="mb-4">
                <label for="payment_date" class="form-label fw-semibold">Tanggal Pembayaran</label>
                <input type="date" name="payment_date" id="payment_date" class="form-control shadow-sm" value="{{ now()->format('Y-m-d') }}">
            </div>

            <!-- Catatan -->
            <div class="mb-4">
                <label for="note" class="form-label fw-semibold">Catatan</label>
                <textarea name="note" id="note" class="form-control shadow-sm" rows="3"></textarea>
            </div>

            <!-- Total Harga -->
            <div class="mt-3 fw-bold fs-5" id="total-price">
                Total: Rp. 0,00
            </div>

            <button type="submit" class="btn btn-success mt-4 w-100">Simpan Transaksi</button>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const addProductBtn = document.getElementById('add-product');
                const productSelection = document.getElementById('product-selection');
                const totalPriceEl = document.getElementById('total-price');

                const formatRupiah = (number) => {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number);
                };

                document.getElementById('supplier_option').addEventListener('change', function() {
                    if (this.value === 'input') {
                        document.getElementById('supplier-select-container').style.display = 'none';
                        document.getElementById('supplier-input-container').style.display = 'block';
                    } else {
                        document.getElementById('supplier-select-container').style.display = 'block';
                        document.getElementById('supplier-input-container').style.display = 'none';
                    }
                });

                addProductBtn.addEventListener('click', function() {
                    const count = document.querySelectorAll('.product-item').length;
                    const newProduct = document.createElement('div');
                    newProduct.classList.add('product-item', 'mb-3');
                    newProduct.innerHTML = `
                        <select name="products[${count}][id]" class="form-select product-id shadow-sm" required>
                            <option value="">Pilih Produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} - Rp. {{ number_format($product->purchase_price, 2) }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="products[${count}][quantity]" class="form-control mt-2 shadow-sm quantity" placeholder="Jumlah" min="1" required>
                        <input type="number" name="products[${count}][price]" class="form-control mt-2 shadow-sm price" placeholder="Harga Satuan" step="any" required>
                        <input type="hidden" name="products[${count}][subtotal]" class="form-control mt-2 subtotal">
                        <button type="button" class="btn btn-danger mt-2 remove-product">Hapus Produk</button>
                    `;
                    productSelection.appendChild(newProduct);
                    updateRemoveButtons();
                });

                function updateRemoveButtons() {
                    document.querySelectorAll('.remove-product').forEach(button => {
                        button.onclick = () => {
                            button.parentElement.remove();
                            updateSubtotal();
                        };
                        button.style.display = 'inline-block';
                    });
                }

                productSelection.addEventListener('input', function(e) {
                    if (e.target.classList.contains('quantity') || e.target.classList.contains('price')) {
                        updateSubtotal();
                    }
                });

                function updateSubtotal() {
                    let total = 0;
                    document.querySelectorAll('.product-item').forEach(item => {
                        const qty = parseFloat(item.querySelector('.quantity').value) || 0;
                        const price = parseFloat(item.querySelector('.price').value) || 0;
                        const subtotal = qty * price;
                        item.querySelector('.subtotal').value = subtotal;
                        total += subtotal;
                    });
                    totalPriceEl.textContent = 'Total: ' + formatRupiah(total);
                }

                updateRemoveButtons();
            });
        </script>
    @endpush
@endsection
