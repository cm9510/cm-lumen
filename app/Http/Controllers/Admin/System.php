<?php
namespace App\Http\Controllers\Admin;

use App\Enums\CommonEnums;
use App\Models\{Roles, Permissions};

class System extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    // 添加|修改角色
    public function editRole()
    {
        $id = intval($this->params['id'] ?? 0);
        $name = trim($this->params['name'] ?? '');
        $desc = trim($this->params['desc'] ?? '');
        $roles = $this->params['roles'] ?? [];
        $status = intval($this->params['status'] ?? 0);
        $permissions = $this->params['permissions'] ?? [];

        if(empty($name)){
            return $this->failJson('请填写角色名称');
        }elseif(mb_strlen($desc) > 30){
            return $this->failJson('角色介绍最多30字');
        }elseif (empty($roles) || !is_array($roles)) {
            return $this->failJson('请填写路由name');
        }elseif (empty($permissions) || !is_array($permissions)) {
            return $this->failJson('请选择权限');
        }elseif (!in_array($status, [0,1])){
            return $this->failJson('状态错误');
        }

        $roles = array_map(function ($v){
            return trim($v);
        }, $roles);
        sort($roles);
        $roles = implode(',', $roles);
        $permissions = array_map(function ($v){
            if(is_numeric($v) && $v){
                return $v;
            }
        }, $permissions);
        sort($permissions);
        $permissions = implode(',', $permissions);

        if($id){ // 修改
            $role = Roles::where(['id'=>$id,'deleted'=>CommonEnums::NORMAL])
                ->select(['id','name','desc','permission_id','roles','status','updator_id'])
                ->first();
            if(empty($role)){
                return $this->failJson('角色不存在');
            }
            $update = [];
            if($role->name != $name) $update['name'] = $name;
            if($role->desc != $desc) $update['desc'] = $desc;
            if($role->permission_id != $permissions) $update['permission_id'] = $permissions;
            if($role->roles != $roles) $update['roles'] = $roles;
            if($role->status != $status) $update['status'] = $status;
            if($update) $update['updator_id'] = $this->aid;
            if(!$update){
                return $this->successJson([],'修改成功');
            }

            $res = $role->update($update);
            $op = '修改';
        }else{
            $res = Roles::insert([
                'name'=>$name,
                'desc'=>$desc,
                'permission_id'=>$permissions,
                'roles'=>$roles,
                'status'=>$status,
                'creator_id'=>$this->aid,
                'create_at'=>$_SERVER['REQUEST_TIME']
            ]);
            $op = '添加';
        }
        return $res ? $this->successJson([],$op.'成功') : $this->failJson($op.'失败');
    }

    // 角色列表
    public function roleList()
    {
        $page = intval($this->params['page'] ?? 1);
        $size = intval($this->params['size'] ?? 20);
        $page = $page > 0 ? ($page-1) * $size : 0;
        $keyword = trim($this->params['keyword'] ?? '');

        $where = [['deleted','=',CommonEnums::NORMAL]];
        if($keyword){
            $where[] = ['name', 'like', '%'.$keyword.'%'];
        }
        $total = Roles::where($where)->count();
        $list = Roles::where($where)->with(['creator','updator'])
            ->select(['id','name','desc','permission_id','roles','status','creator_id','updator_id','create_at'])
            ->offset($page)->limit($size)
            ->get();

        if(!$list->isEmpty()){
            $pid = $list->pluck('permission_id')->toArray();
            $pid = array_unique($pid);
            $pid = explode(',',implode(',',$pid));
            $permissions = Permissions::whereIn('id', $pid)->where(['status'=>0,'deleted'=>CommonEnums::NORMAL])->select(['id','name'])->get();
            $p = [];
            foreach ($permissions as $pv) {
                $p['_'.$pv->id] = $pv;
            }
            foreach ($list as $v) {
                $v->roles = explode(',',$v->roles);
                $v->permission_id = explode(',',$v->permission_id);
                $v->permission = [];
                $v->permission = array_map(function ($pid) use ($p){
                    return $p['_'.$pid];
                }, $v->permission_id);
                unset($v->permission_id, $v->creator_id, $v->updator_id);
            }
        }

        return $this->successJson(['total'=>$total, 'list'=>$list]);
    }

    // 删除角色
    public function delRole()
    {
        $id = intval($this->params['id'] ?? 0);
        $role = Roles::where(['id'=>$id,'deleted'=>CommonEnums::NORMAL])->select(['id','deleted'])->first();
        if(empty($role)){
            return $this->failJson('角色不存在');
        }
        $role->deleted = CommonEnums::DELETE;
        $res = $role->save();
        return $res ? $this->successJson([],'删除成功') : $this->failJson('删除失败');
    }

    // 添加|修改权限
    public function editPermission()
    {
        $id = intval($this->params['id'] ?? 0);
        $name = trim($this->params['name'] ?? '');
        $url = trim($this->params['url'] ?? '');
        $status = intval($this->params['status'] ?? '');
        if(!$name){
            return $this->failJson('权限名不能为空');
        }elseif (!$url){
            return $this->failJson('url不能为空');
        }

        if($id){ // 修改
            $permission = Permissions::where(['id'=>$id,'deleted'=>CommonEnums::NORMAL])->select(['id','name','url','status','creator_id','updator_id'])->first();
            if(empty($permission)){
                return $this->failJson('权限不存在');
            }
            $update = [];
            if($permission->name != $name) $update['name'] = $name;
            if($permission->url != $url) $update['url'] = $url;
            if($permission->status != $status) $update['status'] = $status;
            if($permission->creator_id != $this->aid) $update['updator_id'] = $this->aid;

            if($update){
                $res = $permission->update($update);
            }else{
                return $this->successJson([],'修改成功');
            }
            $op = '修改';
        }else{
            $res = Permissions::insert([
                'name'=> $name,
                'url'=> $url,
                'status'=> $status,
                'creator_id'=>$this->aid,
                'create_at'=>$_SERVER['REQUEST_TIME']
            ]);
            $op = '添加';
        }
        return $res ? $this->successJson([],$op.'成功') : $this->failJson($op.'失败');
    }

    // 权限列表
    public function permissionList()
    {
        $page = intval($this->params['page'] ?? 1);
        $size = intval($this->params['size'] ?? 50);
        $page = $page > 0 ? ($page-1) * $size : 0;
        $keyword = trim($this->params['keyword'] ?? '');

        $where = [['deleted','=',CommonEnums::NORMAL]];
        if($keyword){
            $where[] = ['name', 'like', '%'.$keyword.'%'];
        }
        $total = Permissions::where($where)->count();
        $list = Permissions::where($where)->with(['creator','updator'])
            ->select(['id','name','url','status','creator_id','updator_id','create_at'])
            ->offset($page)->limit($size)
            ->get();

        if(!$list->isEmpty()){
            foreach ($list as $v) {
                unset($v->creator_id, $v->updator_id);
            }
        }

        return $this->successJson(['total'=>$total, 'list'=>$list]);
    }

    // 所有权限
    public function permissionAll()
    {
        $list = Permissions::where(['status'=>0,'deleted'=>CommonEnums::NORMAL])->select(['id','name'])->get();
        return $this->successJson($list);
    }

    // 删除权限
    public function delPermission()
    {
        $id = intval($this->params['id'] ?? 0);
        $permission = Permissions::where(['id'=>$id,'deleted'=>CommonEnums::NORMAL])->select(['id','deleted'])->first();
        if(empty($permission)){
            return $this->failJson('权限不存在');
        }
        $permission->deleted = CommonEnums::DELETE;
        $res = $permission->save();
        return $res ? $this->successJson([],'删除成功') : $this->failJson('删除失败');
    }

    // 添加|修改成员
    public function editMember()
    {

    }

    // 成员列表
    public function memberList()
    {

    }

    // 删除成员
    public function delMember()
    {

    }
}
