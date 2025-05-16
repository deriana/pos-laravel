@extends('layout.app')

@section('content')
    <div class="content-wrapper">
        <div class="container">
            <h2 class="mb-4">Laporan Transaksi Penjualan</h2>
            @if (session('success') && session('sale_id'))
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 1500,
                        didClose: () => {
                            Swal.fire({
                                icon: 'info',
                                title: 'Transaksi Berhasil!',
                                text: 'Apakah Anda ingin mencetak struk?',
                                showCancelButton: true,
                                confirmButtonText: 'Cetak Struk',
                                cancelButtonText: 'Batal',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const w = window.open(
                                        "{{ route('sales.receipt.view', session('sale_id')) }}",
                                        '_blank', 'width=800,height=600');
                                    if (!w) {
                                        Swal.fire('Popup diblokir!', 'Silakan izinkan popup untuk mencetak struk.',
                                            'warning');
                                    }
                                }
                            });
                        }
                    });
                </script>
            @endif

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

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="bg-primary text-white">
                                <tr>
                                   <th class="text-white fw-bol">#</th>
                                    <th class="text-white">Invoice</th>
                                    <th class="text-white">Supplier</th>
                                    <th class="text-white">Tanggal</th>
                                    <th class="text-white">Total</th>
                                    <th class="text-white">Diskon</th>
                                    <th class="text-white">PPN (11%)</th>
                                    <th class="text-white">Grand Total</th>
                                    <th class="text-white">Status</th>
                                    <th class="text-white">Aksi</th>    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $sale)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->customer->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                                        <td>Rp{{ number_format($sale->total, 0, ',', '.') }}</td>
                                        <td>Rp{{ number_format($sale->discount, 0, ',', '.') }}</td>
                                        <td>Rp{{ number_format($sale->tax, 0, ',', '.') }}</td>
                                        <td>Rp{{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                                        <td class="text-capitalize">{{ $sale->payment_status }}</td>
                                        <td>
                                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse"
                                                data-bs-target="#details{{ $sale->id }}" aria-expanded="false"
                                                aria-controls="details{{ $sale->id }}">
                                                <i class="bi bi-info-circle"></i> Detail
                                            </button>
                                            <a href="{{ route('sales.receipt', $sale->id) }}"
                                                class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-file-earmark-pdf"></i> View Invoice
                                            </a>
                                        </td>
                                    </tr>
                                    {{-- Detail Transaksi --}}
                                    <tr class="collapse" id="details{{ $sale->id }}">
                                        <td colspan="9">
                                            <div class="p-3 bg-light rounded">
                                                <h5 class="text-primary">Detail Item:</h5>
                                                <table class="table table-bordered table-sm">
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
                                                                <td>Rp{{ number_format($item->sub_total, 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                                <h5 class="mt-4 text-primary">Pembayaran:</h5>
                                                <table class="table table-bordered table-sm">
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
                                                                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                                                </td>
                                                                <td>{{ ucfirst($payment->payment_methode) }}</td>
                                                                <td>Rp{{ number_format($payment->amount, 0, ',', '.') }}
                                                                </td>
                                                                <td>{{ $payment->note ?? '-' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4">Belum ada pembayaran</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>

                                                @if ($sale->accountsReceivable->isNotEmpty())
                                                    <h5 class="mt-4 text-primary">Piutang:</h5>
                                                    @foreach ($sale->accountsReceivable as $account)
                                                        <table class="table table-bordered table-sm">
                                                            <tr>
                                                                <th width="20%">Jatuh Tempo</th>
                                                                <td>{{ \Carbon\Carbon::parse($account->due_date)->format('d M Y') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Piutang</th>
                                                                <td>Rp{{ number_format($account->amount_due, 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Sudah Dibayar</th>
                                                                <td>Rp{{ number_format($account->amount_paid, 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Status</th>
                                                                <td>
                                                                    {{ ucfirst($account->status) }}
                                                                    @if ($account->status !== 'paid')
                                                                        <div class="mt-2">
                                                                            <button class="btn btn-sm btn-warning"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#payReceivableModal{{ $account->id }}">
                                                                                <i class="bi bi-cash-coin"></i> Bayar
                                                                                Piutang
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal untuk Bayar Piutang -->
            @foreach ($purchases as $sale)
                @foreach ($sale->accountsReceivable as $account)
                    <div class="modal fade" id="payReceivableModal{{ $account->id }}" tabindex="-1"
                        aria-labelledby="payReceivableModalLabel{{ $account->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('sale.pay.debt', $account->id) }}">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Bayar Piutang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Jumlah Bayar</label>
                                            <input type="number" name="amount" class="form-control" required
                                                min="1" step="0.01">
                                        </div>
                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">Metode Pembayaran</label>
                                            <select name="payment_method" class="form-control">
                                                <option value="cash">Cash</option>
                                                <option value="credit">Credit</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="payment_date" class="form-label">Tanggal Bayar</label>
                                            <input type="date" name="payment_date" class="form-control"
                                                value="{{ now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Bayar</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
@endsection
