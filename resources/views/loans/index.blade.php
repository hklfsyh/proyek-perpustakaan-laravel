<!DOCTYPE html>
<html>

<head>
    <title>Manajemen Peminjaman - Perpustakaan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Perpustakaan App</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}">Kategori</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('books.index') }}">Buku</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">Pengguna</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page"
                            href="{{ route('loans.index') }}">Peminjaman</a></li>
                </ul>
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
                        $('#saveBtn').html('Simpan');
                    },
                    error: function (data) {
                        alert('Error: ' + data.responseJSON.message);
                        $('#saveBtn').html('Simpan');
                    }
                });
            });

            $('body').on('click', '.returnLoan', function () {
                var loan_id = $(this).data("id");
                if (confirm("Konfirmasi pengembalian buku?")) {
                    $.ajax({
                        type: "PUT", // Perhatikan tipe method adalah PUT
                        url: "/loans/" + loan_id + "/return", // URL ke route custom kita
                        success: function (data) {
                            table.draw();
                        },
                        error: function (data) { console.log('Error:', data); }
                    });
                }
            });

            $('body').on('click', '.deleteLoan', function () {
                var loan_id = $(this).data("id");
                if (confirm("Yakin ingin menghapus data peminjaman ini?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('loans.store') }}" + '/' + loan_id,
                        success: function (data) { table.draw(); },
                        error: function (data) { console.log('Error:', data); }
                    });
                }
            });
        });
    </script>
</body>

</html>
