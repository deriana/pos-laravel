@extends('layout.app')

@section('content')
    <div class="container-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Products</h1>
            <a href="#" class="btn btn-primary">+ Create Product</a>
        </div>

        <!-- Search + Filter -->
        <form class="row g-3 align-items-center mb-4">
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingInput" placeholder="Search product">
                    <label for="floatingInput">Search</label>
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select">
                    <option value="">All Categories</option>
                    <option value="1">Category A</option>
                    <option value="2">Category B</option>
                    <option value="3">Category C</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-secondary w-100" type="submit">Filter</button>
            </div>
        </form>

        <!-- Product Cards -->
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
            <div class="col">
                <div class="card h-100">
                    <img class="card-img-top" src="img/elements/2.jpg" alt="Product image">
                    <div class="card-body">
                        <h5 class="card-title">Sample Product</h5>
                        <p class="card-text">This is a description of the product. It can be brief or long.</p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="#" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </div>
                </div>
            </div>

            <!-- Repeat for more products... -->
        </div>
    </div>
@endsection
