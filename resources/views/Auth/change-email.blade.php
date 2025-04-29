@extends('layout.auth.app')

@section('content')
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <!-- Change Email Form -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center">
                        <a href="index.html" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <!-- Logo SVG here -->
                            </span>
                            <span class="app-brand-text demo text-body fw-bolder">Sneat</span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <h4 class="mb-2">Change Email</h4>
                    <p class="mb-4">Enter your current email and new email to change it.</p>
                    <form action="{{ route('auth.changeEmailPost') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="current_email" class="form-label">Current Email</label>
                            <input type="email" class="form-control @error('current_email') is-invalid @enderror" id="current_email"
                                   name="current_email" placeholder="Enter your current email" value="{{ old('current_email') }}" required autofocus />
                            @error('current_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_email" class="form-label">New Email</label>
                            <input type="email" class="form-control @error('new_email') is-invalid @enderror" id="new_email"
                                   name="new_email" placeholder="Enter your new email" value="{{ old('new_email') }}" required />
                            @error('new_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary d-grid w-100">Update Email</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('auth.profile') }}" class="d-flex align-items-center justify-content-center">
                            <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                            Back to Profile
                        </a>
                    </div>

                </div>
            </div>
            <!-- /Change Email -->
        </div>
    </div>
@endsection
