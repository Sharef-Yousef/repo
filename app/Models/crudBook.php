<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class crudBook extends Model
{

    protected $fillable = [
        'nameBook',
        'nameAuth',
        'numOfPage',
        'aboutTheBook',
        'category',
        'bookType',
        'bookImage',
        'bookFile',
        'audioFile'
    ];
    use HasFactory;


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function timeTables()
    {
        return $this->hasMany(timeTable::class);
    }
    public function myBook()
    {
        return $this->hasMany(myBook::class);
    }






    use HasFactory;
}
