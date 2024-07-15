<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelf_Books extends Model
{
    use HasFactory;

    protected $fillable = ['bookId','shelfId'];
     protected $table = 'shelf_books';
}
