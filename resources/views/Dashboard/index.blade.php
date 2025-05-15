@extends('layout.app')

@section('content')
    <div class="container-fluid mt-4">
        <h2 class="mb-4">Dashboard UMKM</h2>

        {{-- Ringkasan --}}
        <div class="row gy-4 mb-5">
            <!-- Total Produk -->
            <div class="col-md-3">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Total Produk</h5>
                        <h3 class="mt-auto">{{ $totalProducts }}</h3>
                    </div>
                </div>
            </div>

            <!-- Stok Rendah -->
            <div class="col-md-3">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Stok Rendah</h5>
                        <h3 class="mt-auto">{{ $lowStockProducts }}</h3>
                    </div>
                </div>
            </div>

            <!-- Keuntungan Hari Ini vs Kemarin -->
            <div class="col-md-3">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            <span>Keuntungan Hari Ini</span>
                            <small class="badge bg-light text-dark">
                                @if ($percentChangeFormatted >= 0)
                                    +{{ $percentChangeFormatted }}%
                                @else
                                    {{ $percentChangeFormatted }}%
                                @endif
                            </small>
                        </h5>
                        <h4 class="mt-auto text-success">Rp {{ number_format($profitNow, 0, ',', '.') }}</h4>
                        <p class="mb-0"><small>Kemarin: Rp {{ number_format($profitYesterday, 0, ',', '.') }}</small></p>
                    </div>
                </div>
            </div>

            <!-- Transaksi Hari Ini -->
            <div class="col-md-3">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Transaksi Hari Ini</h5>
                        <h6 class="mt-auto">Penjualan: <span>{{ $saleTransactionsToday }}</span></h6>
                        <h6>Supplier: <span>{{ $purchaseTransactionsToday }}</span></h6>
                    </div>
                </div>
            </div>
        </div>



        {{-- Tabel Produk --}}
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Data Produk</span>
                <select id="productFilter" class="form-select w-auto">
                    <option value="all">Semua</option>
                    <option value="top">Terlaris</option>
                    <option value="low">Stok Rendah</option>
                </select>
            </div>
            <div class="table-responsive p-4">

                <table class="table table-bordered table-striped mb-0" id="productTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Produk</th>
                            <th>Stok</th>
                            <th>Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr data-type="{{ $product->type }}">
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>{{ $product->total_sold }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                <nav>
                    <ul class="pagination">
                        {{-- Previous --}}
                        <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $products->previousPageUrl() ?? '#' }}"
                                tabindex="-1">Previous</a>
                        </li>

                        {{-- Next --}}
                        <li class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $products->nextPageUrl() ?? '#' }}">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>


        {{-- Tabel Transaksi Hari Ini --}}
        <div class="card shadow mb-4">
            <div class="card-header">
                Transaksi Hari Ini
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Nama Pelanggan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                                    <td>{{ $sale->customer->name }}</td>
                                    <td>Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                     <div class="d-flex justify-content-center mt-3">
                <nav>
                    <ul class="pagination">
                        {{-- Previous --}}
                        <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $products->previousPageUrl() ?? '#' }}"
                                tabindex="-1">Previous</a>
                        </li>

                        {{-- Next --}}
                        <li class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $products->nextPageUrl() ?? '#' }}">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-secondary text-white">
                <span>Grafik Penjualan</span>
                <select id="chartFilter" class="form-select w-auto">
                    <option value="week">Minggu Ini</option>
                    <option value="month">Bulan Ini</option>
                    <option value="year">Tahun Ini</option>
                </select>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartData = {
        week: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            data: @json($weekSalesData)
        },
        month: {
            labels: ['1-7', '8-14', '15-21', '22-28', '29-31'],
            data: @json($monthSalesData)
        },
        year: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            data: @json($yearSalesData)
        }
    };


        const ctx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.week.labels,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: chartData.week.data,
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    ticks: {
                        callback: value => 'Rp ' + value.toLocaleString()
                    }
                }
            }
        }
    });

        // Chart Filter Logic
      document.getElementById('chartFilter').addEventListener('change', function() {
        const selected = this.value;
        const newData = chartData[selected];
        salesChart.data.labels = newData.labels;
        salesChart.data.datasets[0].data = newData.data;
        salesChart.update();
    });

        // Produk Filter
        document.getElementById('productFilter').addEventListener('change', function() {
            const selected = this.value;
            const rows = document.querySelectorAll('#productTable tbody tr');

            rows.forEach(row => {
                if (selected === 'all') {
                    row.style.display = '';
                } else if (row.getAttribute('data-type') === selected) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
@endsection
