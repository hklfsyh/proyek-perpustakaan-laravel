<!DOCTYPE html>
<html>

<head>
    <title>CRUD Kategori - Perpustakaan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
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
                        <a class="nav-link active" aria-current="page" href="{{ route('categories.index') }}">Manajemen Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('books.index') }}">Manajemen Buku</a>
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
                <h1 class="mb-4">Manajemen Kategori Buku</h1>
                <a class="btn btn-success mb-3" href="javascript:void(0)" id="createNewCategory"> Tambah Kategori
                    Baru</a>
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kategori</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
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
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

<script type="text/javascript">
    $(function () {

        // Setup AJAX Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Render DataTable
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('categories.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ]
        });

        // Event Klik Tombol "Tambah Kategori Baru"
        $('#createNewCategory').click(function () {
            $('#saveBtn').val("create-category");
            $('#category_id').val('');
            $('#categoryForm').trigger("reset");
            $('#modelHeading').html("Tambah Kategori Baru");
            $('#ajaxModel').modal('show');
        });

        // Event Klik Tombol Edit
        $('body').on('click', '.editCategory', function () {
            var category_id = $(this).data('id');
            $.get("{{ route('categories.index') }}" + '/' + category_id + '/edit', function (data) {
                $('#modelHeading').html("Edit Kategori");
                $('#saveBtn').val("edit-category");
                $('#ajaxModel').modal('show');
                $('#category_id').val(data.id);
                $('#name').val(data.name);
            })
        });

        // Event Klik Tombol Simpan
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
                    $('#saveBtn').html('Simpan Perubahan');
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#saveBtn').html('Simpan Perubahan');
                }
            });
        });

        // Event Klik Tombol Delete
        $('body').on('click', '.deleteCategory', function () {

            var category_id = $(this).data("id");
            if (confirm("Apakah Anda Yakin ingin menghapus data ini?")) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('categories.store') }}" + '/' + category_id,
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

</html>
