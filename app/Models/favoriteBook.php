<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class favoriteBook extends Model
{
    protected $fillable = [
        'userId',
        'bookId'
    ];
    use HasFactory;
    public function user(){
        return $this->belongsTo(User::class);
    }
}
