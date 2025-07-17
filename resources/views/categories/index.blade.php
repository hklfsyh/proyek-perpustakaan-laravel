<!DOCTYPE html>
<html>

<head>
    <title>Manajemen Kategori - Perpustakaan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body
    data-can-manage-library="{{ auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->role == 'librarian') ? 'true' : 'false' }}">

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
                        @endif
                        <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                                href="{{ route('catalog.index') }}">Buku</a></li>
                        @if(Auth::user()->role == 'admin' || Auth::user()->role == 'librarian')
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
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">Manajemen Kategori Buku</h1>
                @if(auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->role == 'librarian'))
                    <a class="btn btn-success mb-3" href="javascript:void(0)" id="createNewCategory"> Tambah Kategori
                        Baru</a>
                @endif
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kategori</th>
                            @if(auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->role == 'librarian'))
                                <th width="20%">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm" name="categoryForm" class="form-horizontal">
                        <input type="hidden" name="category_id" id="category_id">
                        <div class="form-group">
                            <label for="name" class="col-sm-4 control-label">Nama Kategori</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Masukkan Nama Kategori" value="" maxlength="50" required="">
                            </div>
                        </div>
                        <div class="col-sm-offset-2 col-sm-10 mt-3">
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Simpan
                                Perubahan</button>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const canManageLibrary = document.body.dataset.canManageLibrary === 'true';

            var columns = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
            ];

            if (canManageLibrary) {
                columns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
            }

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('categories.index') }}",
                columns: columns
            });

            if (canManageLibrary) {
                $('#createNewCategory').click(function () {
                    $('#saveBtn').val("create-category");
                    $('#category_id').val('');
                    $('#categoryForm').trigger("reset");
                    $('#modelHeading').html("Tambah Kategori Baru");
                    $('#ajaxModel').modal('show');
                });

                $('body').on('click', '.editCategory', function () {
                    var category_id = $(this).data('id');
                    $.get("{{ url('categories') }}" + '/' + category_id + '/edit', function (data) {
                        $('#modelHeading').html("Edit Kategori");
                        $('#saveBtn').val("edit-category");
                        $('#ajaxModel').modal('show');
                        $('#category_id').val(data.id);
                        $('#name').val(data.name);
                    })
                });

                $('#saveBtn').click(function (e) {
                    e.preventDefault();
                    $(this).html('Sending..');
                    $.ajax({
                        data: $('#categoryForm').serialize(),
                        url: "{{ route('categories.store') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            $('#categoryForm').trigger("reset");
                            $('#ajaxModel').modal('hide');
                            table.draw();
                            // NOTIFIKASI SUKSES BARU
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.success,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#saveBtn').html('Simpan Perubahan');
                        },
                        error: function (data) {
                            console.log('Error:', data);
                            // NOTIFIKASI ERROR BARU
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Terjadi kesalahan! ' + (data.responseJSON.message || 'Error tidak diketahui'),
                            });
                            $('#saveBtn').html('Simpan Perubahan');
                        }
                    });
                });

                $('body').on('click', '.deleteCategory', function () {
                    var category_id = $(this).data("id");

                    // POPUP KONFIRMASI BARU
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
                            // Jika dikonfirmasi, jalankan AJAX untuk hapus
                            $.ajax({
                                type: "DELETE",
                                url: "{{ route('categories.store') }}" + '/' + category_id,
                                success: function (data) {
                                    table.draw();
                                    Swal.fire(
                                        'Dihapus!',
                                        'Data Anda telah dihapus.',
                                        'success'
                                    )
                                },
                                error: function (data) {
                                    console.log('Error:', data);
                                    Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                                }
                            });
                        }
                    })
                });
            }
        });
    </script>
</body>

</html>
