<!DOCTYPE html>
<html>

<head>
    <title>Manajemen Peminjaman - Perpustakaan</title>
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
        <h1>Manajemen Peminjaman</h1>
        <a class="btn btn-success mb-3" href="javascript:void(0)" id="createNewLoan"> Catat Peminjaman Baru</a>
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Judul Buku</th>
                    <th>Peminjam</th>
                    <th>Petugas</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="loanForm" name="loanForm" class="form-horizontal">
                        <div class="form-group mb-3">
                            <label for="book_id" class="control-label">Buku</label>
                            <select name="book_id" id="book_id" class="form-control" required>
                                <option value="">Pilih Buku</option>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}">{{ $book->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="member_id" class="control-label">Anggota Peminjam</label>
                            <select name="member_id" id="member_id" class="form-control" required>
                                <option value="">Pilih Anggota</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="librarian_id" class="control-label">Petugas</label>
                            <select name="librarian_id" id="librarian_id" class="form-control" required>
                                <option value="">Pilih Petugas</option>
                                @foreach($librarians as $librarian)
                                    <option value="{{ $librarian->id }}">{{ $librarian->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="note" class="control-label">Catatan</label>
                            <textarea name="note" id="note" class="form-control"></textarea>
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

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('loans.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'book_title', name: 'book.title' },
                    { data: 'member_name', name: 'member.name' },
                    { data: 'librarian_name', name: 'librarian.name' },
                    { data: 'loan_at_formatted', name: 'loan_at' },
                    { data: 'returned_at_formatted', name: 'returned_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $('#createNewLoan').click(function () {
                $('#loanForm').trigger("reset");
                $('#modelHeading').html("Catat Peminjaman Baru");
                $('#ajaxModel').modal('show');
            });

            $('#loanForm').on('submit', function (e) {
                e.preventDefault();
                $('#saveBtn').html('Sending..');
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('loans.store') }}",
                    type: "POST",
                    success: function (data) {
                        $('#loanForm').trigger("reset");
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

            $('body').on('click', '.returnLoan', function () {
                var loan_id = $(this).data("id");
                Swal.fire({
                    title: 'Konfirmasi Pengembalian',
                    text: "Anda yakin buku ini sudah dikembalikan?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Sudah Kembali!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "PUT",
                            url: "/loans/" + loan_id + "/return",
                            success: (data) => {
                                table.draw();
                                Swal.fire('Berhasil!', 'Buku telah ditandai kembali.', 'success');
                            },
                            error: (data) => Swal.fire('Error!', 'Gagal memproses pengembalian.', 'error')
                        });
                    }
                });
            });

            $('body').on('click', '.deleteLoan', function () {
                var loan_id = $(this).data("id");
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data peminjaman ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('loans.store') }}" + '/' + loan_id,
                            success: (data) => {
                                table.draw();
                                Swal.fire('Dihapus!', 'Data peminjaman telah dihapus.', 'success');
                            },
                            error: (data) => Swal.fire('Error!', 'Gagal menghapus data.', 'error')
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>
