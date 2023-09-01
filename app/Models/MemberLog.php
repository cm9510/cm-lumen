<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function member(): BelongsTo
    {
        return $this->belongsTo('App\Models\Members','member_id')->select(['id','nickname']);
    }

}
