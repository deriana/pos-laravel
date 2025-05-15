@extends('layout.app')

@section('content')
    <div class="content-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Users</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Tambah User</button>
        </div>
        @if (session()->has('success'))
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
        <div class="card p-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>PhoneNumber</th>
                        <th>Address</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->phone_number }}</td>
                            <td>{{ $user->address }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal"
                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}" data-role="{{ $user->role }}"
                                    data-address="{{ $user->address }}" data-phone-number="{{ $user->phone_number }}">
                                    Edit
                                </button>

                                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#changePasswordModal" data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}">
                                    Ganti Password
                                </button>

                                <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $user->id }})">
                                    Hapus
                                </button>

                                <form id="deleteForm{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}"
                                    method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah User -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="addUserForm" action="#" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Tambah User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="addname" class="form-label">name</label>
                            <input type="text" class="form-control" id="addname" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="addEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="addEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAddress" class="form-label">Address</label>
                            <input type="text" class="form-control" id="addAddress" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="addPhoneNumber" class="form-label">Number Phone</label>
                            <input type="text" class="form-control" id="addPhoneNumber" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="addRole" class="form-label">Role</label>
                            <select class="form-select" id="addRole" name="role" required>
                                <option value="kasir">Kasir</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="addPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="addPassword" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editUserForm" action="#" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="id" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editname" class="form-label">name</label>
                            <input type="text" class="form-control" id="editname" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress" class="form-label">address</label>
                            <input type="text" class="form-control" id="editAddress" name="address" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhoneNumber" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="editPhoneNumber" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">Role</label>
                            <select class="form-select" id="editRole" name="role" required>
                                <option value="kasir">Kasir</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="changePasswordForm" action="" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="changePasswordUserId" name="id" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">Ganti Password User: <span
                                id="changePasswordname"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="newPassword" name="password" required
                                minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="confirmPassword"
                                name="password_confirmation" required minlength="6">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary">Ganti Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Konfirmasi hapus user
        function confirmDelete(userId) {
            Swal.fire({
                title: 'Yakin hapus user ini?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    // submit form hapus
                    document.getElementById('deleteForm' + userId).submit();
                }
            });
        }

        // Isi form edit user saat modal edit muncul
        var editUserModal = document.getElementById('editUserModal')
        editUserModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var email = button.getAttribute('data-email');
            var role = button.getAttribute('data-role');
            var address = button.getAttribute('data-address');
            var phoneNumber = button.getAttribute('data-phone-number')

            document.getElementById('editUserId').value = id;
            document.getElementById('editname').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            document.getElementById('editAddress').value = address;
            document.getElementById('editPhoneNumber').value = phoneNumber

            // form action bisa disesuaikan, contoh:
            document.getElementById('editUserForm').action = '/users/' + id;
        });

        // Isi form ganti password saat modal muncul
        var changePasswordModal = document.getElementById('changePasswordModal');
        changePasswordModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var userId = button.getAttribute('data-id');
            var userName = button.getAttribute('data-name');

            // Set nama user di modal
            var modalTitleName = changePasswordModal.querySelector('#changePasswordname');
            modalTitleName.textContent = userName;

            // Set value input hidden user id
            var inputUserId = changePasswordModal.querySelector('#changePasswordUserId');
            inputUserId.value = userId;

            // Set action form dengan id user
            var form = changePasswordModal.querySelector('#changePasswordForm');
            form.action = '/users/' + userId + '/change-password';
        });


        // Validasi konfirmasi password di form ganti password
        var changePasswordForm = document.getElementById('changePasswordForm');
        changePasswordForm.addEventListener('submit', function(e) {
            var pass = document.getElementById('newPassword').value;
            var confirmPass = document.getElementById('confirmPassword').value;
            if (pass !== confirmPass) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Password dan konfirmasi password tidak sama!',
                });
            }
        });
    </script>
@endsection
