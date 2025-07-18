# 📚 Aplikasi Manajemen Perpustakaan (Full-Stack)

<p align="center">
  <em>Sebuah aplikasi web full-stack untuk manajemen perpustakaan yang dibangun dari nol sebagai bagian dari tes seleksi. Aplikasi ini mencakup fungsionalitas CRUD yang lengkap, sistem autentikasi, serta hak akses berbasis peran untuk Admin, Pustakawan, dan Anggota.</em>
</p>

---

## 🚀 Fitur Utama

-   **Manajemen Data (CRUD):**
    -   ✅ CRUD **Kategori** Buku.
    -   ✅ CRUD **Katalog Buku** dengan relasi *many-to-many* (satu buku bisa memiliki banyak kategori).
    -   ✅ CRUD **Pengguna** dengan 3 peran berbeda (Admin, Pustakawan, Anggota).
    -   ✅ CRUD **Peminjaman** Buku, lengkap dengan fitur pengembalian.

-   **Autentikasi & Otorisasi:**
    -   ✅ Halaman Login & Registrasi yang aman menggunakan **Laravel Breeze**.
    -   ✅ Proteksi halaman (middleware) untuk memastikan hanya pengguna terautentikasi yang dapat mengakses data manajemen.
    -   ✅ **Hak Akses Dinamis** berdasarkan 3 peran.

-   **Fitur Pengguna & UX:**
    -   ✅ **Katalog Publik** untuk pengunjung (*guest*).
    -   ✅ **Peminjaman Mandiri** untuk Anggota.
    -   ✅ Tampilan menu dan tombol yang adaptif sesuai peran pengguna.

-   **Antarmuka Modern:**
    -   ✅ Operasi data **tanpa *refresh*** halaman (AJAX).
    -   ✅ Tabel interaktif dengan pencarian, urutan, dan paginasi *server-side* (**DataTables**).
    -   ✅ Notifikasi *pop-up* yang indah dan informatif (**SweetAlert2**).
    -   ✅ *Dropdown* multi-pilih yang canggih untuk kategori buku (**Select2**).

---

## 🛠️ Teknologi yang Digunakan

-   **Backend**: Laravel
-   **Frontend**: Bootstrap 5, jQuery, AJAX
-   **Database**: MySQL
-   **Tooling**: Vite, Composer, NPM
-   **Paket Utama**:
    -   `laravel/breeze` (Autentikasi)
    -   `yajra/laravel-datatables` (Tabel Data)
    -   `sweetalert2` (Notifikasi)
    -   `select2` (Dropdown)

---

## ⚙️ Panduan Instalasi & Setup

Untuk menjalankan proyek ini di lingkungan lokal:

1.  **Clone repositori ini:**
    ```bash
    git clone [https://github.com/NAMA_USER_ANDA/NAMA_REPO_ANDA.git](https://github.com/NAMA_USER_ANDA/NAMA_REPO_ANDA.git)
    cd NAMA_REPO_ANDA
    ```

2.  **Instal dependensi PHP:**
    ```bash
    composer install
    ```

3.  **Salin dan konfigurasi file environment:**
    ```bash
    cp .env.example .env
    ```
    Buka file `.env` dan sesuaikan pengaturan database Anda (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

4.  **Generate application key:**
    ```bash
    php artisan key:generate
    ```

5.  **Jalankan migrasi dan seeder:**
    Perintah ini akan menghapus semua tabel dan mengisinya dengan 3 akun pengguna default (Admin, Librarian, Member).
    **Peringatan: Perintah ini akan menghapus semua data yang ada di database.**
    ```bash
    php artisan migrate:fresh --seed
    ```

6.  **Instal dependensi JavaScript:**
    ```bash
    npm install
    ```

7.  **Jalankan Vite development server:**
    Buka satu terminal dan biarkan perintah ini tetap berjalan.
    ```bash
    npm run dev
    ```

8.  **Jalankan server Laravel:**
    Buka terminal **baru** dan jalankan perintah ini.
    ```bash
    php artisan serve
    ```
    Buka aplikasi di browser pada alamat `http://127.0.0.1:8000`.

---

## 👤 Akun Demo

Anda dapat menggunakan akun berikut untuk menguji aplikasi dengan berbagai hak akses:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@perpustakaan.app` | `password` |
| **Librarian** | `librarian@perpustakaan.app` | `password` |
| **Member** | `member@perpustakaan.app` | `password` |
