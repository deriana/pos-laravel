@extends('layout.app')

@section('content')
    <div class="content-wrapper">
        <div class="container">
            <h2 class="mb-4">Laporan Transaksi Penjualan</h2>

            {{-- Filter Status --}}
            <form method="GET" class="mb-4">
                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label">Filter Status:</label>
                    <div class="col-sm-4">
                        <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="row">
                @foreach ($purchases as $sale)
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <strong>Invoice: {{ $sale->invoice_number }}</strong> - 
                                {{ $sale->customer->name ?? 'Pelanggan Tidak Diketahui' }}
                            </div>
                            <div class="card-body mt-4">
                                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</p>
                                <p><strong>Total:</strong> Rp{{ number_format($sale->total, 0, ',', '.') }}</p>
                                <p><strong>Diskon:</strong> Rp{{ number_format($sale->discount, 0, ',', '.') }}</p>
                                <p><strong>PPN (11%):</strong> Rp{{ number_format($sale->tax, 0, ',', '.') }}</p>
                                <p><strong>Grand Total:</strong> Rp{{ number_format($sale->grand_total, 0, ',', '.') }}</p>
                                <p><strong>Status Pembayaran:</strong> {{ strtoupper($sale->payment_status) }}</p>

                                {{-- Detail Item --}}
                                <h5>Detail Item:</h5>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sale->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name ?? 'Produk Terhapus' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                                <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- Pembayaran --}}
                                <h5 class="mt-4">Pembayaran:</h5>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Metode</th>
                                            <th>Jumlah</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($sale->payments as $payment)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                                                <td>{{ ucfirst($payment->payment_method) }}</td>
                                                <td>Rp{{ number_format($payment->amount, 0, ',', '.') }}</td>
                                                <td>{{ $payment->note ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">Belum ada pembayaran</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                {{-- Piutang (Jika Ada) --}}
                                @if (method_exists($sale, 'accountsPayable') && $sale->accountsPayable->isNotEmpty())
                                    <h5 class="mt-4">Piutang / Hutang:</h5>
                                    @foreach ($sale->accountsPayable as $account)
                                        <table class="table table-striped">
                                            <tr>
                                                <th>Jatuh Tempo</th>
                                                <td>{{ \Carbon\Carbon::parse($account->due_date)->format('d M Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total Piutang</th>
                                                <td>Rp{{ number_format($account->amount_due, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Sudah Dibayar</th>
                                                <td>Rp{{ number_format($account->amount_paid, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>{{ ucfirst($account->status) }}</td>
                                            </tr>
                                        </table>
                                    @endforeach
                                @endif

                                {{-- <div class="text-center mt-4">
                                    <a href="{{ route('sales.receipt', $sale->id) }}" class="btn btn-primary">Unduh Struk (PDF)</a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endsection
