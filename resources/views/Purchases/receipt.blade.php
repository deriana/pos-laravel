<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembelian</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        .receipt {
            width: 260px;
            margin: 0 auto;
            padding: 10px;
        }
        .receipt-header {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }
        .receipt-header p {
            margin: 0;
        }
        .line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .table {
            width: 100%;
            margin-bottom: 10px;
        }
        .table th, .table td {
            padding: 5px 0;
            text-align: left;
        }
        .table th {
            font-weight: normal;
        }
        .table td {
            text-align: right;
        }
        .table td.product {
            text-align: left;
        }
        .total {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <p>STORE NAME</p>
            <p>STRUK PEMBELIAN</p>
            <p><strong>Invoice No:</strong> {{ $purchase->invoice_number }}</p>
            <p><strong>Supplier:</strong> {{ $purchase->supplier->name }}</p>
            <p><strong>Tanggal:</strong> {{ $purchase->sale_date->format('d M Y') }}</p>
        </div>

        <div class="line"></div>

        <h5>Detail Pembelian:</h5>
        <table class="table">
            <thead>
                <tr>
                    <th class="product">Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->purchaseItems as $item)
                <tr>
                    <td class="product">{{ $item->product->name ?? 'Produk Terhapus' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="line"></div>

        <h5>Pembayaran:</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchase->payments as $payment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td>Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="3">Belum ada pembayaran</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="line"></div>

        <div class="total">
            <p><strong>Total:</strong> Rp{{ number_format($purchase->total, 0, ',', '.') }}</p>
            <p><strong>Diskon:</strong> Rp{{ number_format($purchase->discount, 0, ',', '.') }}</p>
            <p><strong>PPN (11%):</strong> Rp{{ number_format($purchase->tax, 0, ',', '.') }}</p>
            <p><strong>Grand Total:</strong> Rp{{ number_format($purchase->grand_total, 0, ',', '.') }}</p>
        </div>

        <div class="footer">
            <p><em>Terima kasih atas pembelian Anda!</em></p>
        </div>
    </div>
</body>
</html>
<script>
    window.onload = function() {
        window.print(); // Memanggil fungsi print setelah halaman sepenuhnya dimuat
    };
</script>
