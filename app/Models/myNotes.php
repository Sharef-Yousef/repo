<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class myNotes extends Model
{
    protected $fillable = [
        'note',
        'userId'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    use HasFactory;
}
