<!DOCTYPE html>
<html>

<head>
    <title>Manajemen Pengguna - Perpustakaan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body data-can-manage-users="{{ auth()->check() && auth()->user()->role == 'admin' ? 'true' : 'false' }}">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('catalog.index') }}">Perpustakaan App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @if(Auth::user()->role == 'admin' || Auth::user()->role == 'librarian')
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                                    href="{{ route('categories.index') }}">Kategori</a></li>
                            <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                                    href="{{ route('catalog.index') }}">Buku</a></li>
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}"
                                    href="{{ route('loans.index') }}">Peminjaman</a></li>
                        @endif
                        @if(Auth::user()->role == 'admin')
                            <li class="nav-item"><a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                                    href="{{ route('users.index') }}">Pengguna</a></li>
                        @endif
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); this.closest('form').submit();">Log Out</a>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @else
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">Log in</a></li>
                        <li class="nav-item"><a href="{{ route('register') }}" class="nav-link">Register</a></li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Manajemen Pengguna</h1>
        @if(auth()->check() && auth()->user()->role == 'admin')
            <a class="btn btn-success mb-3" href="javascript:void(0)" id="createNewUser"> Tambah Pengguna Baru</a>
        @endif
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    @if(auth()->check() && auth()->user()->role == 'admin')
                        <th width="15%">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm" name="userForm" class="form-horizontal">
                        <input type="hidden" name="user_id" id="user_id">
                        <div class="form-group mb-3">
                            <label for="name" class="control-label">Nama</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="control-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="role" class="control-label">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="admin">Admin</option>
                                <option value="librarian">Librarian</option>
                                <option value="member">Member</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="phone" class="control-label">Telepon</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="form-group mb-3">
                            <label for="address" class="control-label">Alamat</label>
                            <textarea name="address" id="address" class="form-control"></textarea>
                        </div>
                        <hr>
                        <p class="text-muted">Isi password hanya jika ingin mengubahnya.</p>
                        <div class="form-group mb-3">
                            <label for="password" class="control-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="control-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation">
                        </div>
                        <div class="col-sm-offset-2 col-sm-10 mt-3">
                            <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            const canManageUsers = document.body.dataset.canManageUsers === 'true';

            var columns = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role' },
            ];

            if (canManageUsers) {
                columns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
            }

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}",
                columns: columns
            });

            if (canManageUsers) {
                $('#createNewUser').click(function () {
                    $('#userForm').trigger("reset");
                    $('#modelHeading').html("Tambah Pengguna Baru");
                    $('#ajaxModel').modal('show');
                });

                $('body').on('click', '.editUser', function () {
                    var user_id = $(this).data('id');
                    $.get("{{ url('users') }}" + '/' + user_id + '/edit', function (data) {
                        $('#modelHeading').html("Edit Pengguna");
                        $('#user_id').val(data.id);
                        $('#name').val(data.name);
                        $('#email').val(data.email);
                        $('#phone').val(data.phone);
                        $('#address').val(data.address);
                        $('#role').val(data.role);
                        $('#ajaxModel').modal('show');
                    });
                });

                $('#userForm').on('submit', function (e) {
                    e.preventDefault();
                    $('#saveBtn').html('Sending..');
                    $.ajax({
                        data: $(this).serialize(),
                        url: "{{ route('users.store') }}",
                        type: "POST",
                        success: function (data) {
                            $('#userForm').trigger("reset");
                            $('#ajaxModel').modal('hide');
                            table.draw();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.success,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#saveBtn').html('Simpan');
                        },
                        error: function (data) {
                            Swal.fire('Error!', data.responseJSON.message, 'error');
                            $('#saveBtn').html('Simpan');
                        }
                    });
                });

                $('body').on('click', '.deleteUser', function () {
                    var user_id = $(this).data("id");
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data yang sudah dihapus tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: "DELETE",
                                url: "{{ route('users.store') }}" + '/' + user_id,
                                success: (data) => {
                                    table.draw();
                                    Swal.fire('Dihapus!', 'Data pengguna telah dihapus.', 'success');
                                },
                                error: (data) => Swal.fire('Error!', 'Gagal menghapus data.', 'error')
                            });
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>
