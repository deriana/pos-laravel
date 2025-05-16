<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>{{ env('APP_NAME') }} | Invoice</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
    @php
        $path = public_path('image.jpeg');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    @endphp

    <div class="invoice-box" style="font-family: Arial, sans-serif; max-width: 800px; margin: auto;">
        <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <tr class="top">
                <td colspan="2" style="padding: 10px 0;">
                    <table style="width: 100%;">
                        <tr>
                            <td class="title" style="width: 50%;">
                                <img src="{{ $base64 }}" style="width: 100%; max-width: 300px;" alt="Logo" />
                            </td>

                            <td style="text-align: right;">
                                Invoice #: {{ $purchase->invoice_number }}<br />
                                Created: {{ $purchase->sale_date->format('d M Y') }}<br />
                                Due: {{ $purchase->sale_date->copy()->addMonth()->format('d M Y') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2" style="padding: 10px 0;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 50%;">
                                {{ $purchase->supplier->name ?? '-' }}<br />
                                {{ $purchase->supplier->address ?? '-' }}<br />
                                {{ $purchase->supplier->phone_number ?? '-' }}
                            </td>

                            <td style="text-align: right; width: 50%;">
                                {{ $purchase->user->name ?? 'Unknown User' }}<br />
                                {{ $purchase->user->email ?? '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading" style="background: #eee; border-bottom: 1px solid #ddd;">
                <td style="padding: 5px;">
                    Payment Method 
                </td>
                <td style="padding: 5px; text-align: right;">Amount Paid</td>
            </tr>

            <tr class="details">
                <td style="padding: 5px;">{{ $purchase->payments->first()?->payment_methode ?? '-' }}</td>
                <td style="padding: 5px; text-align: right;">
                    @php
                        $totalPaid = $purchase->payments->sum('amount');
                    @endphp
                    Rp {{ number_format($totalPaid, 2, ',', '.') }}
                </td>
            </tr>

            <tr class="heading" style="background: #eee; border-bottom: 1px solid #ddd; margin-top: 20px;">
                <td style="padding: 5px;">Item</td>
                <td style="padding: 5px; text-align: right;">Price</td>
            </tr>

            @foreach ($purchase->purchaseItems as $item)
                <tr class="item">
                    <td style="padding: 5px;">
                        {{ $item->product->name ?? 'Produk tidak ditemukan' }}<br>
                        <small>Qty: {{ $item->quantity }} {{ $item->product->unit ?? '' }}</small>
                    </td>
                    <td style="padding: 5px; text-align: right;">
                        Rp {{ number_format($item->subtotal, 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach

            <tr class="total" style="font-weight: bold; border-top: 2px solid #333;">
                <td style="padding: 5px; text-align: right;">Subtotal:</td>
                <td style="padding: 5px; text-align: right;">
                    Rp {{ number_format($purchase->total, 2, ',', '.') }}
                </td>
            </tr>

            <tr class="total" style="font-weight: bold;">
                <td style="padding: 5px; text-align: right;">Diskon ({{ $purchase->discount }}%):</td>
                <td style="padding: 5px; text-align: right;">
                    - Rp {{ number_format(($purchase->total * $purchase->discount) / 100, 2, ',', '.') }}
                </td>
            </tr>

            <tr class="total" style="font-weight: bold;">
                <td style="padding: 5px; text-align: right;">Pajak (11%)</td>
                <td style="padding: 5px; text-align: right;">
                    Rp {{ number_format($purchase->tax, 2, ',', '.') }}
                </td>
            </tr>

            <tr class="total" style="font-weight: bold; border-top: 2px solid #333;">
                <td style="padding: 5px; text-align: right;">Grand Total:</td>
                <td style="padding: 5px; text-align: right;">
                    Rp {{ number_format($purchase->grand_total, 2, ',', '.') }}
                </td>
            </tr>

            @if ($purchase->note)
                <tr>
                    <td colspan="2" style="padding: 10px; font-style: italic;">
                        Catatan: {{ $purchase->note }}
                    </td>
                </tr>
            @endif
        </table>
    </div>

</body>

</html>
