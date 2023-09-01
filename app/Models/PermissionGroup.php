<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermissionGroup extends Model
{
    protected $table = 'cm_permission_group';

    protected $fillable = [
        'key',
        'name',
        'sort',
        'created_at'
    ];

    public $timestamps = false;

    /**
     * 所有权限
     * @return HasMany
     */
    public function permissions(): HasMany
    {
        return $this->hasMany('App\Models\Permissions','group_id');
    }

}
