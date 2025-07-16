<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    // Di tabel loans tidak ada created_at/updated_at, jadi kita matikan fitur timestamps
    public $timestamps = false;

    protected $fillable = [
        'book_id',
        'member_id',
        'librarian_id',
        'loan_at',
        'returned_at',
        'note',
    ];

    // Relasi ke model Book (satu peminjaman hanya untuk satu buku)
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Relasi ke model User sebagai peminjam (member)
    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    // Relasi ke model User sebagai petugas (librarian)
    public function librarian()
    {
        return $this->belongsTo(User::class, 'librarian_id');
    }
}
