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
        @if (session()->has('success'))
            <p class="alert alert-success">{{ session('success') }}</p>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });
            </script>
        @endif
        <!-- Search + Filter -->
        <form class="row g-3 align-items-center mb-4" action="{{ route('products.index') }}" method="GET">
            <div class="col-md-6">
                <div class="form-floating h-100">
                    <input type="text" class="form-control" id="floatingInput" placeholder="Search product"
                        name="search" value="{{ request('search') }}">
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
                            <img class="card-img-top"
                                src="{{ $product->product_image ? url('storage/images/' . $product->product_image) : asset('img/box-icon.jpg') }}"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"
                                alt="Product {{ $product->name }}">
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


                                    <div class="d-flex mb-3 align-items-center">
                                        <strong>Stock:</strong>
                                        <p class="mb-0 me-2">{{ $product->stock }} {{ $product->unit }}</p>
                                        <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-primary">+</a>
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
                            {{-- <button class="btn btn-sm btn-primary">Add To Cart <i class='tf-icons bx bx-cart'></i></button> --}}
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                data-bs-target="#barCodeModal{{ $product->id }}">
                                View BarCode
                            </button>
                            <form id="delete-form-{{ $product->id }}"
                                action="{{ route('products.destroy', $product->id) }}" method="POST"
                                style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>

                            <button class="btn btn-sm btn-danger"
                                onclick="confirmDelete({{ $product->id }})">Delete</button>
                        </div>
                    </div>

                </div>

                <div class="modal fade" id="barCodeModal{{ $product->id }}" tabindex="-1"
                    aria-labelledby="barCodeModalLabel{{ $product->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="barCodeModalLabel{{ $product->id }}">BarCode Code for
                                    {{ $product->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                @if ($product->barCode && $product->barCode->filename)
                                    <img src="{{ asset('storage/barcodes/' . $product->barCode->filename) }}"
                                        alt="BarCode" class="img-fluid">
                                    {{-- <p>{{ $product->barCodecode->filename }}</p> --}}
                                @else
                                    <p>No BarCode available for this product.</p>
                                @endif
                            </div>
                            <div class="modal-footer">
                                @if ($product->barCode && $product->barCode->filename)
                                    <button class="btn btn-primary"
                                        onclick="printBarcode('{{ asset('storage/barcodes/' . $product->barCode->filename) }}')">
                                        Print
                                    </button>
                                @else
                                    <form action="{{ route('products.generateBarcode', $product->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success">Generate Barcode</button>
                                    </form>
                                @endif
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p>No products found.</p>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->onEachSide(0)->links('pagination::simple-bootstrap-5') }}
        </div>
    </div>
    <script>
        function printBarcode(imageUrl) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
            <html>
                <head>
                    <title>Print Barcode</title>
                    <style>
                        body { text-align: center; margin-top: 50px; }
                        img { max-width: 100%; height: auto; }
                    </style>
                </head>
                <body>
                    <img src="${imageUrl}" onload="window.print(); window.close();">
                </body>
            </html>
        `);
            printWindow.document.close();
        }

        function confirmDelete(productId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This product will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                allowOutsideClick: false,
                allowEscapeKey: false,
                backdrop: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + productId).submit();
                }
            });
        }
    </script>
@endsection
