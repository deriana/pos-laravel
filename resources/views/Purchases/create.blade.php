@extends('layout.app')

@section('content')
    <div class="container">
        <h2>Form Pembelian</h2>
        <form action="{{ route('purchases.store') }}" method="POST">
            @csrf

            <!-- Supplier -->
            <div class="mb-3">
                <label for="supplier_id" class="form-label">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-control" required>
                    <option value="">Pilih Supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Produk -->
            <div class="mb-3">
                <label for="products" class="form-label">Pilih Produk</label>
                <div id="product-selection">
                    <div class="product-item mb-3">
                        <select name="products[0][id]" class="form-control product-id" required>
                            <option value="">Pilih Produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} - Rp.
                                    {{ number_format($product->purchase_price, 2) }} - {{ $product->stock }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="products[0][quantity]" class="form-control mt-2 quantity"
                            placeholder="Jumlah" min="1" required>
                        <input type="number" name="products[0][price]" class="form-control mt-2 price"
                            placeholder="Harga Satuan" step="any" required>
                        <input type="hidden" name="products[0][subtotal]" class="form-control mt-2 subtotal">
                        <button type="button" class="btn btn-danger mt-2 remove-product" style="display:none;">Hapus
                            Produk</button>
                    </div>
                </div>
                <button type="button" id="add-product" class="btn btn-primary mt-2">Tambah Produk</button>
            </div>
            <!-- Diskon (Persen) -->
            <div class="mb-3">
                <label for="discount" class="form-label">Diskon (%)</label>
                <input type="number" name="discount" id="discount" class="form-control" step="any" min="0"
                    max="100" value="0">
            </div>

            <!-- Pajak (PPN) -->
            <div class="mb-3">
                <label class="form-label">PPN (Pajak 11%)</label>
                <input type="text" class="form-control" value="11%" readonly>
            </div>

            <!-- Jumlah Dibayar -->
            <div class="mb-3">
                <label for="amount_paid" class="form-label">Jumlah Dibayar</label>
                <input type="number" name="amount_paid" id="amount_paid" class="form-control" min="0"
                    step="any">
            </div>

            <!-- Metode Pembayaran -->
            <div class="mb-3">
                <label for="payment_method" class="form-label">Metode Pembayaran</label>
                <select name="payment_method" id="payment_method" class="form-control">
                    <option value="cash">Cash</option>
                    <option value="credit">Credit</option>
                </select>
            </div>

            <!-- Tanggal Pembayaran -->
            <div class="mb-3">
                <label for="payment_date" class="form-label">Tanggal Pembayaran</label>
                <input type="date" name="payment_date" id="payment_date" class="form-control"
                    value="{{ now()->format('Y-m-d') }}">
            </div>

            <!-- Catatan -->
            <div class="mb-3">
                <label for="note" class="form-label">Catatan</label>
                <textarea name="note" id="note" class="form-control" rows="3"></textarea>
            </div>

            <div class="mt-3" id="total-price">
                Total: Rp. 0.00
            </div>

            <button type="submit" class="btn btn-success">Simpan Transaksi</button>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const addProductBtn = document.getElementById('add-product');
                const productSelection = document.getElementById('product-selection');

                // Tambah produk baru
                addProductBtn.addEventListener('click', function() {
                    const productCount = document.querySelectorAll('.product-item').length;
                    const newProductItem = document.createElement('div');
                    newProductItem.classList.add('product-item', 'mb-3');
                    newProductItem.innerHTML = `
                <select name="products[${productCount}][id]" class="form-control product-id" required>
                    <option value="">Pilih Produk</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} - Rp. {{ number_format($product->purchase_price, 2) }}</option>
                    @endforeach
                </select>
                <input type="number" name="products[${productCount}][quantity]" class="form-control mt-2 quantity" placeholder="Jumlah" min="1" required>
                <input type="number" name="products[${productCount}][price]" class="form-control mt-2 price" placeholder="Harga Satuan"  step="any" required>
                <input type="hidden" name="products[${productCount}][subtotal]" class="form-control mt-2 subtotal" >
                <button type="button" class="btn btn-danger mt-2 remove-product">Hapus Produk</button>
            `;
                    productSelection.appendChild(newProductItem);

                    // Update event untuk tombol hapus
                    updateRemoveButtons();
                });

                // Update event untuk tombol hapus produk
                function updateRemoveButtons() {
                    document.querySelectorAll('.remove-product').forEach(button => {
                        button.addEventListener('click', function() {
                            this.parentElement.remove();
                        });
                    });
                }

                // Update subtotal dan harga saat quantity atau price berubah
                productSelection.addEventListener('input', function(e) {
                    if (e.target.classList.contains('quantity') || e.target.classList.contains('price')) {
                        updateSubtotal();
                    }
                });

                function updateSubtotal() {
                    document.querySelectorAll('.product-item').forEach((item, index) => {
                        const quantity = item.querySelector('.quantity').value;
                        const price = item.querySelector('.price').value;
                        const subtotal = quantity * price;
                        item.querySelector('.subtotal').value = subtotal;

                        // Update total harga produk
                        let total = 0;
                        document.querySelectorAll('.subtotal').forEach(sub => {
                            total += parseFloat(sub.value) || 0;
                        });

                        // Tampilkan total produk
                        document.getElementById('total-price').textContent = 'Total: Rp. ' + total.toFixed(2);
                    });
                }

                // Inisialisasi tombol hapus
                updateRemoveButtons();
            });
        </script>
    @endpush
@endsection
