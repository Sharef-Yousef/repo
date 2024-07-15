<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class evalBook extends Model
{
    protected $fillable = [
        'userId',
        'bookId',
        'evalBook'
    ];
    public function users(){
        return $this->belongsTo(user::class);
    }
    use HasFactory;
}
