    @extends('layout.app')

    @section('content')
        {{-- Ganti bg-primary dengan background image --}}
        <div class="py-5 mb-5"
            style="background: url('{{ asset('img/logo/' . config('app.logo')) }}') center center / cover no-repeat;">
            <div class="container text-white text-shadow">
                <h3 class="fw-bold">Store Settings</h3>
                <p class="lead">Update your store configuration below.</p>
            </div>
        </div>



        <div class="container">
            @if ($errors->any())
                <div class="container mb-4">
                    <div class="alert alert-danger rounded-3">
                        <h5 class="mb-2">Whoops! There were some problems with your input:</h5>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-md-8">

                    {{-- Card dengan efek mengambang di atas background --}}
                    <div class="card shadow-lg border-0 rounded-4"
                        style="margin-top: -100px; z-index: 10; position: relative;">
                        <h5 class="card-header bg-white border-bottom-0 fw-bold fs-4">Edit Settings</h5>

                        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="card-body">

                                {{-- Preview logo --}}
                                <div class="text-start mb-4">
                                    <img src="{{ asset(config('app.logo')) }}" alt="{{ config('app.name') }}"
                                        class="img-fluid rounded" style="max-height: 100px;">
                                    <p class="mt-2 text-muted small">Current Logo</p>
                                </div>

                                <div class="mb-3">
                                    <label for="app_name" class="form-label fw-semibold">App Name</label>
                                    <input type="text" class="form-control @error('app_name') is-invalid @enderror"
                                        id="app_name" name="app_name" value="{{ old('app_name', config('app.name')) }}"
                                        required>
                                    @error('app_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tax" class="form-label fw-semibold">Tax (%)</label>
                                    <input type="number" class="form-control @error('tax') is-invalid @enderror"
                                        id="tax" name="tax" value="{{ old('tax', 11) }}" required>
                                    @error('tax')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Example: Enter <code>11</code> for 11% tax.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="app_logo" class="form-label fw-semibold">Upload Logo</label>

                                    <!-- Preview Gambar -->
                                    <div class="mb-3">
                                        <img id="logoPreview" src="{{ asset('img/logo/' . config('app.logo')) }}"
                                            alt="Logo Preview" height="100">
                                    </div>

                                    <!-- Input File -->
                                    <input type="file" class="form-control @error('app_logo') is-invalid @enderror"
                                        id="app_logo" name="app_logo" accept="image/*">
                                    @error('app_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">File format: <code>jpg, png, jpeg</code>. Example:
                                        <code>logo.jpeg</code></small>
                                </div>

                                <!-- Script untuk preview gambar -->
                                <script>
                                    document.getElementById('app_logo').addEventListener('change', function(event) {
                                        const [file] = event.target.files;
                                        if (file) {
                                            document.getElementById('logoPreview').src = URL.createObjectURL(file);
                                        }
                                    });
                                </script>


                            </div>

                            <div class="card-footer text-end bg-white border-top-0">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    @endsection
