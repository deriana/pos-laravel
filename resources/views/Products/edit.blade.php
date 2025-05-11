@extends('layout.app')

@section('content')
    <div class="container-wrapper">
        <h1>Edit Product</h1>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <!-- Product Name -->
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="productName" name="name" placeholder="Product Name" value="{{ old('name', $product->name) }}" required>
                        <label for="productName">Product Name</label>
                    </div>
                </div>

                <!-- Category -->
                <div class="col-md-6">
                    <div class="form-floating">
                        <select class="form-select" id="category" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <label for="category">Category</label>
                    </div>
                </div>

                <!-- Price -->
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="number" class="form-control" id="purchasePrice" name="purchase_price" placeholder="Purchase Price" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                        <label for="purchasePrice">Purchase Price</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="number" class="form-control" id="sellingPrice" name="selling_price" placeholder="Selling Price" value="{{ old('selling_price', $product->selling_price) }}" required>
                        <label for="sellingPrice">Selling Price</label>
                    </div>
                </div>

                <!-- Stock -->
                {{-- <div class="col-md-6">
                    <div class="form-floating">
                        <input type="number" class="form-control" id="stock" name="stock" placeholder="Stock" value="{{ old('stock', $product->stock) }}" required>
                        <label for="stock">Stock</label>
                    </div>
                </div> --}}

                <!-- Unit -->
                <div class="col-md-6">
                    <div class="form-floating">
                        <select class="form-select" id="unit" name="unit" required>
                            <option value="">Select Unit</option>
                            <option value="pcs" {{ old('unit', $product->unit) == 'pcs' ? 'selected' : '' }}>Pcs</option>
                            <option value="box" {{ old('unit', $product->unit) == 'box' ? 'selected' : '' }}>Box</option>
                            <option value="liter" {{ old('unit', $product->unit) == 'liter' ? 'selected' : '' }}>Liter</option>
                        </select>
                        <label for="unit">Unit</label>
                    </div>
                </div>                
                
                <!-- Product Image Upload and Preview -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="productImage">Product Image</label>
                        <input type="file" class="form-control" id="productImage" name="product_image" accept="image/*" onchange="previewImage(event)">
                        <div id="imagePreview" class="mt-2">
                            @if($product->product_image)
                                <img src="{{ url('storage/images/' . $product->product_image) }}" alt="Product Image" class="img-fluid" style="max-width: 150px;">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100">Update Product</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = ''; // Clear previous preview

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('img-fluid');
                    img.style.maxWidth = '150px';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
