<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Userwallet extends Model
{
    //
    protected $table = "user_wallet";
    
    //protected $primaryKey = ""; change if you need to change
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
