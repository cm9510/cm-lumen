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
        'updator_id',
        'redirect',
        'create_at'
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
    public function updator()
    {
        return $this->hasOne('App\Models\Members','id','updator_id')->select(['id','nickname','phone']);
    }

}
