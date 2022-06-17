<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberRoleRelation extends Model
{
    protected $table = 'cm_role_member';

    protected $fillable = ['member_id', 'role_id', 'create_at'];

    public $timestamps = false;

    /**
     * 管理角色
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany('App\Models\Roles','admin_id')
            ->where(['status'=>0,'deleted'=>0])
            ->select(['id','name','roles']);
    }

}
