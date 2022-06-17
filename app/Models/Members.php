<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    protected $table = 'cm_members';

    protected $fillable = [
        'nickname',
        'phone',
        'password',
        'salt',
        'status',
        'last_login_at',
        'create_at'
    ];

    public $timestamps = false;

}
