@extends('layout.app')

@section('content')
    <div class="content-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Suppliers</h1>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                Tambah Supplier
            </button>
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
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->phone_number }}</td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->address }}</td>
                            <td>
                                <!-- Edit Supplier Button -->
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editSupplierModal"
                                    data-id="{{ $supplier->id }}" data-name="{{ $supplier->name }}"
                                    data-phone="{{ $supplier->phone_number }}" data-email="{{ $supplier->email }}"
                                    data-address="{{ $supplier->address }}">
                                    Edit
                                </button>

                                <!-- Delete Supplier Button -->
                                <form id="deleteForm{{ $supplier->id }}"
                                    action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger"
                                        onclick="confirmDelete({{ $supplier->id }})">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <!-- Add Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplierModalLabel">Tambah Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Supplier</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSupplierModalLabel">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSupplierForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="editName" class="form-label">Nama Supplier</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" id="editPhone" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress" class="form-label">Alamat</label>
                            <textarea class="form-control" id="editAddress" name="address" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // SweetAlert untuk konfirmasi delete
        function confirmDelete(categoryId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Kategori ini akan dihapus permanen.",
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
                    document.getElementById('deleteForm' + categoryId).submit();
                }
            });
        }

        // Mengisi form edit dengan data kategori yang dipilih
        var editSupplierModal = document.getElementById('editSupplierModal')
        editSupplierModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Tombol Edit
            var supplierId = button.getAttribute('data-id');
            var supplierName = button.getAttribute('data-name');
            var supplierPhone = button.getAttribute('data-phone');
            var supplierEmail = button.getAttribute('data-email');
            var supplierAddress = button.getAttribute('data-address');

            // Isi form edit dengan data
            var form = document.getElementById('editSupplierForm');
            form.action = '/suppliers/' + supplierId; // Update action form

            document.getElementById('editName').value = supplierName;
            document.getElementById('editPhone').value = supplierPhone;
            document.getElementById('editEmail').value = supplierEmail;
            document.getElementById('editAddress').value = supplierAddress;
        });
    </script>
@endsection
