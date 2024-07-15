<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'number_phone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    //Favorite
    public function Fav()
    {
        return $this->hasMany(favoriteBook::class);
    }
    //NoteBook
    public function Note()
    {
        return $this->hasMany(noteBook::class);
    }
    //Question
    public function Ques()
    {
        return $this->hasMany(question::class);
    }
    //Evalithon
    public function Eval()
    {
        return $this->hasMany(evalBook::class);
    }
    //CrudBook
    public function crudBook()
    {
        return $this->hasMany(crudBook::class);
    }
    //MyNotes
    public function Notes()
    {
        return $this->hasMany(myNotes::class);
    }
    //TimeTable
    public function timeTables()
    {
        return $this->hasMany(timeTable::class);
    }
    //My Book
    public function myBook()
    {
        return $this->hasMany(myBook::class);
    }
    public function books()
    {
        return $this->belongsTo(crudBook::class);
    }
}
