<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberLog extends Model
{
    protected $table = 'cm_member_log';

    protected $fillable = [
        'member_id',
        'title',
        'detail',
        'ip',
        'request',
        'created_at'
    ];

    public $timestamps = false;

}
