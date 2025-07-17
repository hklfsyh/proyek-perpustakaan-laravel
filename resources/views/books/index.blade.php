<!DOCTYPE html>
<html>

<head>
    <title>Katalog Buku - Perpustakaan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Katalog Buku</h1>
                    @if(auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->role == 'librarian'))
                        <a class="btn btn-success" href="javascript:void(0)" id="createNewBook"> Tambah Buku Baru</a>
                    @endif
                </div>
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>ISBN</th>
                            @if (auth()->check())
                                <th width="15%">
                                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'librarian')
                                        Aksi
                                    @else
                                        Pinjam
                                    @endif
                                </th>
                            @else
                                <th width="15%">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    @if(auth()->check() && (auth()->user()->role == 'admin' || auth()->user()->role == 'librarian'))
        <div class="modal fade" id="ajaxModel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modelHeading"></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="bookForm" name="bookForm" class="form-horizontal">
                            <input type="hidden" name="book_id" id="book_id">
                            <div class="form-group mb-3">
                                <label for="title" class="col-sm-4 control-label">Judul Buku</label>
                                <div class="col-sm-12"><input type="text" class="form-control" id="title" name="title"
                                        required></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="authors" class="col-sm-4 control-label">Penulis</label>
                                <div class="col-sm-12"><input type="text" class="form-control" id="authors" name="authors"
                                        required></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="isbn" class="col-sm-4 control-label">ISBN</label>
                                <div class="col-sm-12"><input type="text" class="form-control" id="isbn" name="isbn"></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="description" class="col-sm-4 control-label">Deskripsi</label>
                                <div class="col-sm-12"><textarea id="description" name="description" class="form-control"
                                        rows="4"></textarea></div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="categories" class="col-sm-4 control-label">Kategori</label>
                                <div class="col-sm-12">
                                    <select class="form-control" id="categories" name="categories[]" multiple="multiple">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-offset-2 col-sm-10 mt-3">
                                <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            const canManageLibrary = document.body.dataset.canManageLibrary === 'true';

            var columns = [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'authors', name: 'authors' },
                { data: 'categories', name: 'categories', orderable: false, searchable: false },
                { data: 'isbn', name: 'isbn' },
            ];

            if ("{{ Auth::check() }}") {
                columns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
            }

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('catalog.index') }}",
                columns: columns
            });

            if (canManageLibrary) {
                $('#categories').select2({ theme: "bootstrap-5", dropdownParent: $('#ajaxModel') });

                $('#createNewBook').click(function () {
                    $('#bookForm').trigger("reset");
                    $('#categories').val(null).trigger('change');
                    $('#modelHeading').html("Tambah Buku Baru");
                    $('#ajaxModel').modal('show');
                });

                $('body').on('click', '.editBook', function () {
                    var book_id = $(this).data('id');
                    $.get("{{ url('books') }}" + '/' + book_id + '/edit', function (data) {
                        $('#modelHeading').html("Edit Buku");
                        $('#book_id').val(data.id);
                        $('#title').val(data.title);
                        $('#authors').val(data.authors);
                        $('#isbn').val(data.isbn);
                        $('#description').val(data.description);
                        var category_ids = data.categories.map(c => c.id);
                        $('#categories').val(category_ids).trigger('change');
                        $('#ajaxModel').modal('show');
                    });
                });

                $('#bookForm').on('submit', function (e) {
                    e.preventDefault();
                    $('#saveBtn').html('Sending..');
                    $.ajax({
                        data: $(this).serialize(),
                        url: "{{ route('books.store') }}",
                        type: "POST",
                        success: (data) => {
                            $('#bookForm').trigger("reset");
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
                        error: (data) => {
                            Swal.fire('Error!', data.responseJSON.message, 'error');
                            $('#saveBtn').html('Simpan');
                        }
                    });
                });

                $('body').on('click', '.deleteBook', function () {
                    var book_id = $(this).data("id");
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
                                url: "{{ route('books.store') }}" + '/' + book_id,
                                success: function (data) {
                                    table.draw();
                                    Swal.fire('Dihapus!', 'Data buku telah dihapus.', 'success');
                                },
                                error: (data) => Swal.fire('Error!', 'Gagal menghapus data.', 'error')
                            });
                        }
                    });
                });
            }

            $('body').on('click', '.borrowBook', function () {
                var book_id = $(this).data("id");
                Swal.fire({
                    title: 'Konfirmasi Peminjaman',
                    text: "Anda yakin ingin meminjam buku ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Pinjam!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "{{ url('borrow') }}/" + book_id,
                            success: function (data) {
                                table.draw();
                                Swal.fire('Berhasil!', data.success, 'success');
                            },
                            error: function (data) {
                                if (data.status === 401) {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Anda harus login terlebih dahulu untuk meminjam buku.',
                                        icon: 'error'
                                    }).then(() => window.location.href = "{{ route('login') }}");
                                } else {
                                    Swal.fire('Error!', data.responseJSON.error || 'Terjadi kesalahan.', 'error');
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>
</body>

</html>
