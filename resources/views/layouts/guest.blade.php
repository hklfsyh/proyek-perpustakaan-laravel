<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Menghilangkan scrollbar horizontal jika ada */
        html,
        body {
            overflow-x: hidden;
        }

        /* Definisi animasi fade-in dan slide-up */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Kelas untuk menerapkan animasi */
        .animate-fade-in-up {
            /* Mengisi state akhir, agar tidak kembali ke state awal */
            animation: fadeInUp 0.7s ease-out forwards;
            /* Mulai dengan transparan agar animasi terlihat */
            opacity: 0;
        }

        /* Kelas untuk transisi yang halus */
        .smooth-transition {
            transition: all 0.3s ease-in-out;
        }

        /* Efek hover untuk semua tombol dan efek focus untuk input */
        .btn:hover,
        .form-control:focus {
            transform: translateY(-3px);
            /* Sedikit terangkat */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15) !important;
            /* Bayangan lebih jelas */
        }

        /* Memberi transisi pada form control */
        .form-control {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row" style="min-height: 100vh;">

            <div class="col-lg-5 col-md-6 d-flex align-items-center justify-content-center">
                <div class="w-100" style="max-width: 400px;">

                    <div class="text-center mb-4 animate-fade-in-up">
                        <a href="/">
                            <x-application-logo style="width: 4rem; height: 4rem; margin: auto; color: #6c757d;" />
                        </a>
                    </div>

                    <div class="card shadow-sm border-0 animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="card-body p-4 p-md-5">
                            {{ $slot }}
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-7 col-md-6 d-none d-md-flex align-items-center justify-content-center"
                style="background: linear-gradient(135deg, #28a745, #218838); color: white;">
                <div class="text-center p-5">
                    <h1 class="display-4 fw-bold animate-fade-in-up" style="animation-delay: 0.4s;">Perpustakaan Digital
                    </h1>
                    <p class="lead mt-3 animate-fade-in-up" style="animation-delay: 0.6s;">Kelola koleksi buku, data
                        anggota, dan proses peminjaman dengan mudah dan efisien.</p>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
