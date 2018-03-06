<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usernotification extends Model
{
    //
    protected $table = "user_notification_log";

    //protected $primaryKey = ""; change if you need to change

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'target_user_id', 
        'notification_type', 
        'notification_detail', 
        'notification_url'
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
