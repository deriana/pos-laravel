@extends('layout.app')

@section('content')
    <div class="container my-4">
        <h1>Profit Report</h1>

        <form class="mb-4" method="GET" action="{{ route('reports.profit') }}">
            <div class="row g-3 align-items-end">
                <div class="col-auto">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control">
                </div>
                <div class="col-auto">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('reports.profit') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
        <form action="{{ route('reports.profit.export') }}" method="GET" class="mb-3">
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <button type="submit" class="btn btn-success">Export to Excel</button>
        </form>


        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sale Date</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Purchase Price</th>
                    <th>Selling Price</th>
                    <th>Subtotal</th>
                    <th>Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report as $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($row['sale_date'])->format('Y-m-d') }}</td>
                        <!-- Tanggal -->
                        <td>{{ $row['product_name'] }}</td>
                        <td>{{ $row['quantity'] }}</td>
                        <td>{{ number_format($row['purchase_price'], 2) }}</td>
                        <td>{{ number_format($row['selling_price'], 2) }}</td>
                        <td>{{ number_format($row['subtotal'], 2) }}</td>
                        <td>{{ number_format($row['profit'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-end">Total Profit</th>
                    <th>{{ number_format($totalProfit, 2) }}</th>
                </tr>
            </tfoot>
        </table>
        <div class="d-flex justify-content-center">
            {{ $saleItems->onEachSide(0)->links('pagination::simple-bootstrap-5') }} </div>
    </div>
@endsection
