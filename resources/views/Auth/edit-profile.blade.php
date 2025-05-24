@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Account</a>
                </li>

            </ul>
            <div class="card mb-4">
                <h5 class="card-header">Profile Details</h5>
                <form action="{{ route('auth.updateProfile') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="{{ asset('img/avatars/' . ($selectedAvatar ?? 'default-avatar.jpeg')) }}"
                                alt="user-avatar" class="d-block rounded" height="100" width="100"
                                id="uploadedAvatar" />

                            <div class="button-wrapper">
                                <label for="avatarSelect" class="form-label">Select a photo</label>
                                <select name="avatar" id="avatarSelect" class="form-select mb-3">
                                    @foreach ($fileNames as $fileName)
                                        <option value="{{ $fileName }}"
                                            {{ $fileName === $selectedAvatar ? 'selected' : '' }}>
                                            {{ $fileName }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Tombol Reset -->
                                <button type="button" class="btn btn-outline-secondary account-image-reset mb-2"
                                    onclick="resetAvatar()">
                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>

                                <!-- Tombol Konfirmasi -->
                                <button type="submit" class="btn btn-success">
                                    <i class="bx bx-check d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Konfirmasi</span>
                                </button>

                                <p class="text-muted mt-2">Pilih avatar dari daftar. Tidak perlu upload.</p>
                            </div>
                        </div>
                    </div>
                </form>


                <script>
                    document.getElementById('avatarSelect').addEventListener('change', function() {
                        var avatarUrl = "{{ asset('img/avatars') }}/" + this.value;
                        document.getElementById('uploadedAvatar').src = avatarUrl;
                    });

                    function resetAvatar() {
                        document.getElementById('avatarSelect').value = '';
                        document.getElementById('uploadedAvatar').src = "{{ asset('img/avatars/default-avatar.jpeg') }}";
                    }
                </script>


                <hr class="my-0" />
                <div class="card-body">
                    <form id="formAccountSettings" method="POST" action="{{ route('auth.updateProfile') }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">First Name</label>
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ Auth::user()->name }}" autofocus />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">ID (+62)</span>
                                    <input type="text" id="phone_number" name="phone_number" class="form-control"
                                        value="{{ Auth::user()->phone_number }}" placeholder="202 555 0111" />
                                </div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="{{ Auth::user()->address }}" placeholder="Address" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control mb-2" type="email" id="email" name="email"
                                    value="{{ Auth::user()->email }}" placeholder="john.doe@example.com" readonly/>
                                    <a href="{{ route('auth.changeEmail') }}" class="btn btn-primary me-2">Change Email</a>

                                <span class="text-muted">You will need to verify your email with OTP</span>
                            </div>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="password" class="form-label">Change Password</label>
                            <input class="form-control mb-2" type="text" id="password" placeholder="*******"
                                readonly="">
                            <a href="{{ route('password.request') }}" class="btn btn-primary me-2">Change Password</a>
                        </div>

                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
@endsection
