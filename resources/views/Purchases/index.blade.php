@extends('layout.app')

@section('content')
    <div class="content-wrapper">
        <div class="container">
            <h2 class="mb-4">Laporan Transaksi Pembelian</h2>
            @if (session('success') && session('purchase_id'))
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
                                        "{{ route('purchases.receipt.view', session('purchase_id')) }}",
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
            @if (session('info'))
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    Swal.fire({
                        icon: 'info',
                        title: 'Info',
                        text: '{{ session('info') }}',
                        confirmButtonText: 'OK'
                    });
                </script>
            @endif
            {{-- Filter Status --}}
           <form method="GET" class="mb-4" action="{{ route('purchases.index') }}">
                <div class="form-group row align-items-center">
                    <label for="status" class="col-sm-2 col-form-label">Filter Status:</label>
                    <div class="col-sm-4">
                        <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        </select>
                    </div>

                    <label for="per_page" class="col-sm-2 col-form-label text-end">Show:</label>
                    <div class="col-sm-2">
                        <select name="per_page" id="per_page" class="form-control" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>

                    <div class="col-sm-2">
                        <span>items per page</span>
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
                                    <th class="text-white">PPN</th>
                                    <th class="text-white">Grand Total</th>
                                    <th class="text-white">Status</th>
                                    <th class="text-white">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $purchase)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $purchase->invoice_number }}</td>
                                        <td>{{ $purchase->supplier->name }}</td>
                                        <td>{{ $purchase->sale_date->format('d M Y') }}</td>
                                        <td>Rp{{ number_format($purchase->total, 0, ',', '.') }}</td>
                                        <td>Rp{{ number_format($purchase->discount, 0, ',', '.') }}</td>
                                        <td>Rp{{ number_format($purchase->tax, 0, ',', '.') }}</td>
                                        <td>Rp{{ number_format($purchase->grand_total, 0, ',', '.') }}</td>
                                        <td class="text-capitalize">{{ $purchase->payment_status }}</td>
                                        <td>
                                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse"
                                                data-bs-target="#details{{ $purchase->id }}" aria-expanded="false"
                                                aria-controls="details{{ $purchase->id }}">
                                                <i class="bi bi-info-circle"></i> Detail
                                            </button>
                                            <a href="{{ route('purchases.receipt', $purchase->id) }}"
                                                class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-file-earmark-pdf"></i> View Invoice
                                            </a>
                                        </td>
                                    </tr>
                                    {{-- Detail Transaksi --}}
                                    <tr class="collapse" id="details{{ $purchase->id }}">
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
                                                        @foreach ($purchase->purchaseItems as $item)
                                                            <tr>
                                                                <td>{{ $item->product->name ?? 'Produk Terhapus' }}</td>
                                                                <td>{{ $item->quantity }}</td>
                                                                <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                                                <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}
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
                                                        @forelse ($purchase->payments as $payment)
                                                            <tr>
                                                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
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

                                                @if ($purchase->accountsPayable->isNotEmpty())
                                                    <h5 class="mt-4 text-primary">Piutang / Hutang:</h5>
                                                    @foreach ($purchase->accountsPayable as $account)
                                                        <table class="table table-bordered table-sm">
                                                            <tr>
                                                                <th width="20%">Jatuh Tempo</th>
                                                                <td>{{ \Carbon\Carbon::parse($account->due_date)->format('d M Y') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Total Hutang</th>
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
                                                                            <a href="{{ route('debt.purchase.confirmPayment', $account->id) }}"
                                                                                class="btn btn-sm btn-warning">
                                                                                <i class="bi bi-cash-coin"></i> Bayar Hutang
                                                                            </a>
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

                         <div class="d-flex justify-content-center">
                            {{ $purchases->onEachSide(0)->links('pagination::simple-bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
