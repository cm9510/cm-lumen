<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'cm_roles';

    protected $fillable = [
        'name',
        'key',
        'desc',
        'permission_id',
        'roles',
        'status',
        'creator_id',
        'updater_id',
        'redirect',
        'created_at'
    ];

    public $timestamps = false;

    /**
     * 关联一个创建人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function creator()
    {
        return $this->hasOne('App\Models\Members','id','creator_id')->select(['id','nickname','phone']);
    }

    /**
     * 关联一个最近修改人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function updater()
    {
        return $this->hasOne('App\Models\Members','id','updater_id')->select(['id','nickname','phone']);
    }

}
