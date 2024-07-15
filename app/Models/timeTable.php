<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class timeTable extends Model
{
    protected $fillable = [
        'bookSize',
        'timePerMinut',
        'userId',
        'bookId',
        'start_date',
        'end_date'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(crudBook::class);
    }


    use HasFactory;
}
