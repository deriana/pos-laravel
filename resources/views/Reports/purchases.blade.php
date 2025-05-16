@extends('layout.app')

@section('content')
    <div class="container my-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Purchase Report</h3>
                <a href="{{ route('reports.purchase.export') }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
                </a>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('reports.purchases') }}"
                    class="mb-4 d-flex align-items-center gap-3 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <label for="per_page" class="form-label mb-0">Items per page:</label>
                        <select name="per_page" id="per_page" onchange="this.form.submit()"
                            class="form-select form-select-sm">
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}" @if ($option == $perPage) selected @endif>
                                    {{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>#</th>
                                <th>Supplier</th>
                                <th>User</th>
                                <th>Purchase Date</th>
                                <th>Invoice Number</th>
                                <th>Total</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Grand Total</th>
                                <th>Profit</th>
                                <th>Payment Status</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $purchase)
                                @php
                                    $profit = $purchase->grand_total - $purchase->total;
                                @endphp
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-start">{{ $purchase->supplier->name ?? '-' }}</td>
                                    <td class="text-start">{{ $purchase->user->name ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->sale_date)->format('d M Y') }}</td>
                                    <td>{{ $purchase->invoice_number }}</td>
                                    <td>{{ number_format($purchase->total, 2) }}</td>
                                    <td>{{ number_format($purchase->discount, 2) }}</td>
                                    <td>{{ number_format($purchase->tax, 2) }}</td>
                                    <td class="fw-semibold">{{ number_format($purchase->grand_total, 2) }}</td>
                                    <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                        {{ number_format($profit, 2) }}
                                    </td>
                                    <td>
                                        @if ($purchase->payment_status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($purchase->payment_status == 'partial')
                                            <span class="badge bg-warning text-dark">Partial</span>
                                        @else
                                            <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="text-start">{{ $purchase->note ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted">No purchase data found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $purchases->onEachSide(0)->links('pagination::simple-bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
