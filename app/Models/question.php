<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class question extends Model
{
    protected $fillable = ['userId','bookId','question'];

public function users(){
    return $this->belongsTo(User::class);
}
    use HasFactory;
}
