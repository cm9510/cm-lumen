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

    /**
     * 关联角色
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany('App\Models\MemberRoleRelation','member_id','id')->select(['member_id','role_id']);
    }

}
