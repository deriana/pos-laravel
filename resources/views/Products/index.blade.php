@extends('layout.app')

@section('content')
    <style>
        .hover-effect {
            transition: transform 0.3s ease-in-out;
        }

        .hover-effect:hover {
            transform: translateY(-10px);
            /* Bergerak ke atas */
        }
    </style>

    <div class="container-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Products</h1>
            <a href="{{ route('products.create') }}" class="btn btn-primary">+ Create Product</a>
        </div>

        <!-- Search + Filter -->
        <form class="row g-3 align-items-center mb-4" action="{{ route('products.index') }}" method="GET">
            <div class="col-md-6">
                <div class="form-floating h-100">
                    <input type="text" class="form-control" id="floatingInput" placeholder="Search product" name="search"
                        value="{{ request('search') }}">
                    <label for="floatingInput">Search</label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-floating h-100">
                    <select class="form-select" id="floatingSelect" name="category">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <label for="floatingSelect">Category</label>
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-secondary w-100 h-100" type="submit">Filter</button>
            </div>
        </form>

        <!-- Product Cards -->
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
            @forelse ($products as $product)
                <div class="col">
                    <div class="card h-100 hover-effect">
                        <div class="card-img-container"
                            style="position: relative; width: 100%; padding-bottom: 56.25%; overflow: hidden;">
                            <img class="card-img-top" src="{{ url('storage/images/' . $product->product_image) }}"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                                alt="Product Image">
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-category mb-0">{{ $product->category->name }}</p>
                            </div>
                            <div class="d-flex justify-content-between w-100">
                                <div class="">
                                    <div class="mb-3 d-flex">
                                        <strong>SKU:</strong>
                                        <p class="mb-0">{{ $product->sku }}</p>
                                    </div>


                                    <div class="mb-3 d-flex">
                                        <strong>Stock:</strong>
                                        <p class="mb-0">{{ $product->stock }}</p>
                                    </div>

                                    <div class="mb-3 d-flex">
                                        <strong>Unit:</strong>
                                        <p class="mb-0">{{ $product->unit }}</p>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="mb-3 d-flex">
                                        <strong>Purchase Price:</strong>
                                        <p class="mb-0">Rp {{ number_format($product->purchase_price, 2, ',', '.') }}</p>
                                    </div>

                                    <div class="mb-3 d-flex">
                                        <strong>Selling Price:</strong>
                                        <p class="mb-0">Rp {{ number_format($product->selling_price, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                            <hr>



                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <p>No products found.</p>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>
@endsection
