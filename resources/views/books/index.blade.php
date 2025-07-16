<!DOCTYPE html>
<html>

<head>
    <title>Manajemen Buku - Perpustakaan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Perpustakaan App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}">Manajemen Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route('books.index') }}">Manajemen Buku</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.index') }}">Manajemen
                            Pengguna</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">Manajemen Buku</h1>
                <a class="btn btn-success mb-3" href="javascript:void(0)" id="createNewBook"> Tambah Buku Baru</a>
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>ISBN</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="authors" class="col-sm-4 control-label">Penulis</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="authors" name="authors" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="isbn" class="col-sm-4 control-label">ISBN</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="isbn" name="isbn">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="col-sm-4 control-label">Deskripsi</label>
                            <div class="col-sm-12">
                                <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                            </div>
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

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        $(function () {

            // Setup AJAX Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Inisialisasi Select2
            $('#categories').select2({
                theme: "bootstrap-5",
                dropdownParent: $('#ajaxModel')
            });

            // Render DataTable
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('books.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'title', name: 'title' },
                    { data: 'authors', name: 'authors' },
                    { data: 'categories', name: 'categories' },
                    { data: 'isbn', name: 'isbn' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            // Event Klik Tombol "Tambah Buku Baru"
            $('#createNewBook').click(function () {
                $('#saveBtn').val("create-book");
                $('#book_id').val('');
                $('#bookForm').trigger("reset");
                $('#categories').val(null).trigger('change');
                $('#modelHeading').html("Tambah Buku Baru");
                $('#ajaxModel').modal('show');
            });

            // Event Klik Tombol Edit
            $('body').on('click', '.editBook', function () {
                var book_id = $(this).data('id');
                $.get("{{ route('books.index') }}" + '/' + book_id + '/edit', function (data) {
                    $('#modelHeading').html("Edit Buku");
                    $('#saveBtn').val("edit-book");
                    $('#ajaxModel').modal('show');
                    $('#book_id').val(data.id);
                    $('#title').val(data.title);
                    $('#authors').val(data.authors);
                    $('#isbn').val(data.isbn);
                    $('#description').val(data.description);

                    var category_ids = data.categories.map(function (category) {
                        return category.id;
                    });
                    $('#categories').val(category_ids).trigger('change');
                })
            });

            // Event Klik Tombol Simpan
            $('#bookForm').on('submit', function (e) {
                e.preventDefault();
                $('#saveBtn').html('Sending..');

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('books.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#bookForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();
                        $('#saveBtn').html('Simpan');
                    },
                    error: function (data) {
                        console.log('Error:', data);
                        alert('Error: ' + data.responseJSON.message);
                        $('#saveBtn').html('Simpan');
                    }
                });
            });

            // Event Klik Tombol Delete
            $('body').on('click', '.deleteBook', function () {
                var book_id = $(this).data("id");
                if (confirm("Apakah Anda Yakin ingin menghapus data ini?")) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('books.store') }}" + '/' + book_id,
                        success: function (data) {
                            table.draw();
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                }
            });

        });
    </script>
</body>

</html>
