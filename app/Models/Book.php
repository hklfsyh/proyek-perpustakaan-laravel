<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'authors',
        'isbn',
    ];

    // Relasi many-to-many ke model Category
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_categories');
    }

    // Relasi one-to-many ke model Loan
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
