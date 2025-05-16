<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Receipt - {{ $sales->invoice_number }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            padding: 20px;
        }

        .center {
            text-align: center;
        }

        .items td {
            padding: 4px 0;
        }

        .totals td {
            font-weight: bold;
            padding-top: 10px;
        }

        hr {
            margin: 10px 0;
            border-top: 1px dashed #000;
        }

        table {
            width: 100%;
        }

        .small {
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="center">
        <strong>{{config('app.name')}}</strong><br>
        {{ now()->format('d M Y H:i') }}
    </div>

    <hr>

    <div>
        Kasir : {{ $sales->user->name ?? '-' }} <br>
        No. Invoice : {{ $sales->invoice_number }}<br>
        Tanggal : {{ \Carbon\Carbon::parse($sales->sale_date)->format('d M Y') }}<br>
        Supplier : {{ $sales->customer->name ?? 'Umum' }}<br>
        Status : {{ ucfirst($sales->payment_status) }}
    </div>

    <hr>

    <table class="items">
        @foreach ($sales->items as $item)
            <tr>
                <td colspan="2">
                    {{ $item->product->name ?? 'Produk' }}<br>
                    <small>Qty: {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                </td>
                <td style="text-align: right;">
                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </table>

    <hr>

    <table class="totals">
        <tr>
            <td colspan="2">Diskon</td>
            <td style="text-align: right;">Rp {{ number_format($sales->discount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2">PPN 11%</td>
            <td style="text-align: right;">Rp {{ number_format($sales->tax, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2">Grand Total</td>
            <td style="text-align: right;">Rp {{ number_format($sales->grand_total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <hr>

    <strong>Pembayaran:</strong>
    <table class="small">
        @forelse ($sales->payments as $payment)
            <tr>
                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                <td>{{ ucfirst($payment->payment_methode) }}</td>
                <td style="text-align: right;">Rp {{ number_format($payment->amount, 0, ',', '.') }} - Rp {{ number_format($sales->grand_total, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Belum ada pembayaran</td>
            </tr>
        @endforelse

        @php
            $totalPaid = $sales->payments->sum('amount');
            $grandTotal = $sales->grand_total;
            $change = $totalPaid - $grandTotal;
        @endphp

        @if ($change > 0)
            <tr>
                <td colspan="2"><strong>Kembalian</strong></td>
                <td style="text-align: right;"><strong>Rp {{ number_format($change, 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>
        @endif
    </table>


    @if ($sales->accountsReceivable->isNotEmpty())
        <hr>
        <strong>Piutang:</strong><br>
        @foreach ($sales->accountsReceivable as $account)
            <table class="small">
                <tr>
                    <td>Jatuh Tempo</td>
                    <td>: {{ \Carbon\Carbon::parse($account->due_date)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td>Total Piutang</td>
                    <td>: Rp {{ number_format($account->amount_due, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Sudah Dibayar</td>
                    <td>: Rp {{ number_format($account->amount_paid, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>: {{ ucfirst($account->status) }}</td>
                </tr>
            </table>
        @endforeach
    @endif

    <hr>

    <div class="center">
        Terima kasih telah berbelanja!
    </div>
</body>

</html>
