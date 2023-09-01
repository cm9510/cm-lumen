<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'created_at'
    ];

    public $timestamps = false;

    /**
     * 关联角色
     * @return HasMany
     */
    public function roleIds(): HasMany
    {
        return $this->hasMany('App\Models\MemberRoleRelation','member_id','id')->select(['member_id','role_id']);
    }

}
