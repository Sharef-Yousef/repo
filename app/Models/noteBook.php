<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class noteBook extends Model
{
    protected $fillable  = [
        'userId',
        'bookId',
        'note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    use HasFactory;
}
