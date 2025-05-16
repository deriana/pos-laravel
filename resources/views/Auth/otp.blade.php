@extends('layout.Auth.app')

@section('content')
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <!-- Forgot Password -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center">
                        <a href="#" class="app-brand-link gap-2">
                            <img src="{{ asset('img/logo/' . config('app.logo')) }}" alt="{{ config('app.name') }}"
                                class="img-fluid rounded-circle" style="max-width: 50px;">
                            <span class="app-brand-text demo text-body fw-bolder">{{ config('app.name') }}</span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <h4 class="mb-2">Verify Email? 🔒</h4>
                    <form action="{{ url('verify-otp') }}" method="POST">
                        @csrf

                        <!-- OTP Input -->
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" name="otp" id="otp"
                                class="form-control @error('otp') is-invalid @enderror" required>
                            @error('otp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Verify OTP Button -->
                        <button type="submit" class="btn btn-primary d-grid w-100">Verify OTP</button>
                    </form>

                    <!-- Resend OTP Button -->
                    <div class="text-center mt-3">
                        <form action="{{ url('resend-otp') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-link">Resend OTP</button>
                        </form>
                    </div>

                </div>
            </div>
            <!-- /Forgot Password -->
        </div>
    </div>
@endsection
