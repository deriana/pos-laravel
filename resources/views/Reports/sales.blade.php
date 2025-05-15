@extends('layout.app')

@section('content')
<div class="container my-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Sales Report</h3>
            <a href="{{ route('reports.sales.export') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}" class="mb-4 d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-2">
                    <label for="per_page" class="form-label mb-0">Items per page:</label>
                    <select name="per_page" id="per_page" onchange="this.form.submit()" class="form-select form-select-sm">
                        @foreach($perPageOptions as $option)
                            <option value="{{ $option }}" @if($option == $perPage) selected @endif>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Customer</th>
                            <th>Sale Date</th>
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
                        @forelse($sales as $sale)
                        @php
                            $profit = $sale->grand_total - $sale->total;
                        @endphp
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-start">{{ $sale->user->name ?? '-' }}</td>
                            <td class="text-start">{{ $sale->customer->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                            <td>{{ $sale->invoice_number }}</td>
                            <td>{{ number_format($sale->total, 2) }}</td>
                            <td>{{ number_format($sale->discount, 2) }}</td>
                            <td>{{ number_format($sale->tax, 2) }}</td>
                            <td class="fw-semibold">{{ number_format($sale->grand_total, 2) }}</td>
                            <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                {{ number_format($profit, 2) }}
                            </td>
                            <td>
                                @if($sale->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($sale->payment_status == 'partial')
                                    <span class="badge bg-warning text-dark">Partial</span>
                                @else
                                    <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-start">{{ $sale->note ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted">No sales data found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
