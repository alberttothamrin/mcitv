<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

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
        'password'/*, 'remember_token',*/
    ];

    public function newUser($data_user=array()) 
    {
        if (count($data_user) > 0 && $data_user !== FALSE) {
            $this->name = $data_user['name'];
            $this->email = $data_user['email'];
            $this->password = sha1(md5($data_user['new_password']));
            $this->save();
        }

    }
}
