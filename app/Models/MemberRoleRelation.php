<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberRoleRelation extends Model
{
    protected $table = 'cm_role_member';

    protected $fillable = ['member_id', 'role_id', 'created_at'];

    public $timestamps = false;

    /**
     * 管理角色
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function roles()
    {
        return $this->hasOne('App\Models\Roles','id','member_id')
            ->where(['status'=>0,'deleted_at'=>0])
            ->select(['id','name','key','roles']);
    }

}
