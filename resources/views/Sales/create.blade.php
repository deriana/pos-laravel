@extends('layout.app')

@section('content')
    <div class="container">
        <h2>Form Penjualan</h2>
        <form action="{{ route('sales.store') }}" method="POST">
            @csrf

            <!-- Customer -->
            <div class="mb-3">
                <label for="customer_id" class="form-label">Customer</label>
                <select name="customer_id" id="customer_id" class="form-control" required>
                    <option value="">Pilih Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
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
                                <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">
                                    {{ $product->name }} - Rp. {{ number_format($product->selling_price, 2) }} .
                                    {{ $product->stock }}
                                </option>
                            @endforeach
                        </select>
                        <input type="number" name="products[0][quantity]" class="form-control mt-2 quantity"
                            placeholder="Jumlah" min="1" required>
                        <input type="hidden" name="products[0][price]" class="form-control mt-2 price">
                        <input type="hidden" name="products[0][subtotal]" class="form-control mt-2 subtotal">
                        <button type="button" class="btn btn-danger mt-2 remove-product" style="display:none;">Hapus
                            Produk</button>
                    </div>
                </div>
                <button type="button" id="add-product" class="btn btn-primary mt-2">Tambah Produk</button>
            </div>

            <!-- Diskon -->
            <div class="mb-3">
                <label for="discount" class="form-label">Diskon (%)</label>
                <input type="number" name="discount" id="discount" class="form-control" step="any" min="0"
                    max="100" value="0">
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

            <!-- Tanggal Penjualan -->
            <div class="mb-3">
                <label for="sale_date" class="form-label">Tanggal Penjualan</label>
                <input type="date" name="sale_date" id="sale_date" class="form-control"
                    value="{{ now()->format('Y-m-d') }}">
            </div>

            <!-- Catatan -->
            <div class="mb-3">
                <label for="note" class="form-label">Catatan</label>
                <textarea name="note" id="note" class="form-control" rows="3"></textarea>
            </div>

            <!-- Total Harga -->
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

                addProductBtn.addEventListener('click', function() {
                    const productCount = document.querySelectorAll('.product-item').length;
                    const newProductItem = document.createElement('div');
                    newProductItem.classList.add('product-item', 'mb-3');
                    newProductItem.innerHTML = `
            <select name="products[${productCount}][id]" class="form-control product-id" required>
                <option value="">Pilih Produk</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">
                        {{ $product->name }} - Rp. {{ number_format($product->selling_price, 2) }} . {{ $product->stock }}
                    </option>
                @endforeach
            </select>
            <input type="number" name="products[${productCount}][quantity]" class="form-control mt-2 quantity" placeholder="Jumlah" min="1" required>
            <input type="hidden" name="products[${productCount}][price]" class="form-control mt-2 price"> <!-- Hidden price input -->
            <input type="hidden" name="products[${productCount}][subtotal]" class="form-control mt-2 subtotal">
            <button type="button" class="btn btn-danger mt-2 remove-product">Hapus Produk</button>
        `;
                    productSelection.appendChild(newProductItem);
                    updateRemoveButtons();
                });

                function updateRemoveButtons() {
                    document.querySelectorAll('.remove-product').forEach(button => {
                        button.addEventListener('click', function() {
                            this.parentElement.remove();
                            updateSubtotal();
                        });
                    });
                }

                productSelection.addEventListener('input', function(e) {
                    if (e.target.classList.contains('quantity') || e.target.classList.contains('product-id')) {
                        updateSubtotal();
                    }
                });

                function updateSubtotal() {
                    let total = 0;
                    document.querySelectorAll('.product-item').forEach(item => {
                        const quantity = item.querySelector('.quantity').value;
                        const productId = item.querySelector('.product-id').value;
                        const price = item.querySelector('.product-id').selectedOptions[0].getAttribute(
                            'data-price');
                        const subtotal = (parseFloat(quantity) || 0) * (parseFloat(price) || 0);
                        item.querySelector('.price').value = price; // Set the hidden price field
                        item.querySelector('.subtotal').value = subtotal;
                        total += subtotal;
                    });

                    // Diskon
                    const discount = parseFloat(document.getElementById('discount').value) || 0;
                    const discountedTotal = total - (total * discount / 100);

                    document.getElementById('total-price').textContent = 'Total: Rp. ' + discountedTotal.toFixed(2);
                }

                document.getElementById('discount').addEventListener('input', updateSubtotal);

                updateRemoveButtons();
            });
        </script>
    @endpush
@endsection
