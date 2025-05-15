@extends('layout.app')

@section('content')
<div class="container my-4">
    <h1>Inventory Report</h1>

    <div class="card p-3 mb-4 shadow-sm">
        <form method="GET" action="{{ route('reports.inventory') }}" class="row g-3 align-items-center">

            <div class="col-auto">
                <label for="per_page" class="form-label">Items per page</label>
                <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}" @if($option == $perPage) selected @endif>{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-auto">
                <label for="category_id" class="form-label">Category</label>
                <select name="category_id" id="category_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- All Categories --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-auto">
                <label for="stock_sort" class="form-label">Sort Stock</label>
                <select name="stock_sort" id="stock_sort" class="form-select" onchange="this.form.submit()">
                    <option value="desc" @if(request('stock_sort') == 'desc') selected @endif>Highest to Lowest</option>
                    <option value="asc" @if(request('stock_sort') == 'asc') selected @endif>Lowest to Highest</option>
                </select>
            </div>

            <div class="col-auto flex-grow-1">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or SKU" onkeydown="if(event.key === 'Enter'){this.form.submit()}">
            </div>

            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('reports.inventory') }}" class="btn btn-secondary ms-2">Reset</a>
            </div>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Unit</th>
                        <th>Purchase Price</th>
                        <th>Selling Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td style="width: 120px; max-width: 120px;">
                            <div class="card-img-container" style="position: relative; width: 100%; padding-bottom: 56.25%; overflow: hidden;">
                                <img
                                    class="card-img-top"
                                    src="{{ $product->product_image ? url('storage/images/' . $product->product_image) : asset('images/box-icon.jpg') }}"
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                                    alt="Product {{ $product->name }}">
                            </div>
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>{{ $product->stock }}</td>
                        <td>{{ $product->unit }}</td>
                        <td>{{ number_format($product->purchase_price, 2) }}</td>
                        <td>{{ number_format($product->selling_price, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No products found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
