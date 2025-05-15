@extends('layout.app')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Customers</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCustomerModal">Tambah Customer</button>
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
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone_number }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->address }}</td>
                    <td>
                        <!-- Edit Customer Button -->
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editCustomerModal"
                            data-id="{{ $customer->id }}" data-name="{{ $customer->name }}"
                            data-phone="{{ $customer->phone_number }}" data-email="{{ $customer->email }}"
                            data-address="{{ $customer->address }}">
                            Edit
                        </button>

                        <!-- Delete Customer Button -->
                        <form id="deleteForm{{ $customer->id }}" action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $customer->id }})">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Tambah Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <div class="mb-3">
                            <label for="addName" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="addName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="addPhone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="addPhone" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="addEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="addEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAddress" class="form-label">Address</label>
                            <textarea class="form-control" id="addAddress" name="address" rows="3"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCustomerForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="editPhone" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress" class="form-label">Address</label>
                            <textarea class="form-control" id="editAddress" name="address" rows="3"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Konfirmasi hapus dengan SweetAlert
    function confirmDelete(customerId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data customer ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm' + customerId).submit();
            }
        });
    }

    // Isi form edit saat modal edit muncul
    var editCustomerModal = document.getElementById('editCustomerModal')
    editCustomerModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget; // tombol yang klik edit
        var customerId = button.getAttribute('data-id');
        var name = button.getAttribute('data-name');
        var phone = button.getAttribute('data-phone');
        var email = button.getAttribute('data-email');
        var address = button.getAttribute('data-address');

        var form = document.getElementById('editCustomerForm');
        form.action = '/customers/' + customerId;

        document.getElementById('editName').value = name;
        document.getElementById('editPhone').value = phone;
        document.getElementById('editEmail').value = email;
        document.getElementById('editAddress').value = address;
    });
</script>
@endsection
