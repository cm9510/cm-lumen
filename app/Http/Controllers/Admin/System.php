<?php
namespace App\Http\Controllers\Admin;

use App\Enums\CommonEnums;
use Cm\Tool\Tools;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{MemberRoleRelation, Members, Roles, Permissions};

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
        $key = trim($this->params['key'] ?? '');
        $redirect = trim($this->params['redirect'] ?? '');
        $desc = trim($this->params['desc'] ?? '');
        $roles = $this->params['roles'] ?? [];
        $status = intval($this->params['status'] ?? 0);
        $permissions = $this->params['permissions'] ?? [];

        if(empty($name)){
            return $this->failJson('请填写角色名称');
        }elseif (empty($key)){
            return $this->failJson('请填写key');
        }elseif(mb_strlen($desc) > 30){
            return $this->failJson('角色介绍最多30字');
        }elseif (empty($roles) || !is_array($roles)) {
            return $this->failJson('请填写路由name');
        }elseif (empty($redirect)){
            return $this->failJson('请填写跳转路由');
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
            return null;
        }, $permissions);
        sort($permissions);
        $permissions = implode(',', $permissions);

        if($id){ // 修改
            $role = Roles::where(['id'=>$id,'deleted'=>CommonEnums::NORMAL])
                ->select(['id','name','key','desc','permission_id','roles','status','updator_id','redirect'])
                ->first();
            if(empty($role)){
                return $this->failJson('角色不存在');
            }
            $update = [];
            if($role->name != $name) $update['name'] = $name;
            if($role->key != $key) $update['key'] = $key;
            if($role->redirect != $redirect) $update['redirect'] = $redirect;
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
                'key'=>$key,
                'desc'=>$desc,
                'permission_id'=>$permissions,
                'roles'=>$roles,
                'status'=>$status,
                'creator_id'=>$this->aid,
                'redirect'=>$redirect,
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
            ->select(['id','name','key','desc','permission_id','roles','status','creator_id','updator_id','redirect','create_at'])
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
                $_permission = [];
                foreach ($v->permission_id as $_pid) {
                    if(isset($p['_'.$_pid])){
                        $_permission[] = $p['_'.$_pid];
                    }
                }
                $v->permission = $_permission;
                unset($v->permission_id, $v->creator_id, $v->updator_id);
            }
        }

        return $this->successJson(['total'=>$total, 'list'=>$list]);
    }

    // 所有角色
    public function rolesAll()
    {
        $list = Roles::where(['status'=>0,'deleted'=>CommonEnums::NORMAL])->select(['id','name'])->get();
        return $this->successJson($list);
    }

    // 添加|修改权限
    public function editPermission()
    {
        $id = intval($this->params['id'] ?? 0);
        $name = trim($this->params['name'] ?? '');
        $url = trim($this->params['url'] ?? '');
        $status = intval($this->params['status'] ?? '');
        $log = intval($this->params['log'] ?? '');
        if(!$name){
            return $this->failJson('权限名不能为空');
        }elseif (!$url){
            return $this->failJson('url不能为空');
        }

        if($id){ // 修改
            $permission = Permissions::where(['id'=>$id,'deleted'=>CommonEnums::NORMAL])->select(['id','name','url','status','log','creator_id','updator_id'])->first();
            if(empty($permission)){
                return $this->failJson('权限不存在');
            }
            $update = [];
            if($permission->name != $name) $update['name'] = $name;
            if($permission->url != $url) $update['url'] = $url;
            if($permission->status != $status) $update['status'] = $status;
            if($permission->log != $status) $update['log'] = $log;
            if($permission->creator_id != $this->aid) $update['updator_id'] = $this->aid;

            if($update){
                $res = $permission->update($update);
            }else{
                return $this->successJson([],'已修改');
            }
            $op = '修改';
        }else{
            $res = Permissions::insert([
                'name'=> $name,
                'url'=> $url,
                'status'=> $status,
                'log'=> $log,
                'creator_id'=> $this->aid,
                'create_at'=> $_SERVER['REQUEST_TIME']
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
            $where[] = [function($query) use($keyword){
                $query->where('name','like','%'.$keyword.'%')->orWhere('url','like','%'.$keyword.'%');
            }];
        }
        $total = Permissions::where($where)->count();
        $list = Permissions::where($where)->with(['creator','updator'])
            ->select(['id','name','url','status','log','creator_id','updator_id','create_at'])
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

    // 添加|修改成员
    public function editMember()
    {
        $id = intval($this->params['id'] ?? 0);
        $nick = trim($this->params['nickname'] ?? '');
        $phone = trim($this->params['phone'] ?? '');
        $password = trim($this->params['password'] ?? '');
        $status = intval($this->params['status'] ?? 0);
        $role = $this->params['roles'] ?? [];

        if(!$nick){
            return $this->failJson('请填写昵称');
        }elseif (!preg_match('/^1[356789]\d{9}$/',$phone)){
            return $this->failJson('请填写正确的手机号');
        }elseif(!$password){
            return $this->failJson('请填写密码');
        }elseif (!is_numeric($status) || !in_array($status,[1,0])){
            return $this->failJson('状态错误');
        }elseif (empty($role)){
            return $this->failJson('请分配角色');
        }
        $role = array_map(function ($r){
            if(is_numeric($r) && $r) return $r;
        }, $role);

        DB::beginTransaction();
        try {
            if($id){ // 修改
                $member = Members::where(['id'=>$id,'deleted'=>CommonEnums::NORMAL])
                    ->select(['id','nickname','phone','salt','status'])
                    ->first();
                if(empty($member)){
                    return $this->failJson('成员不存在');
                }
                $update = [];
                if($member->nickname != $nick) $update['nickname'] = $nick;
                if($member->phone != $phone) $update['phone'] = $phone;
                if($member->status != $status) $update['status'] = $status;
                if($member->password != md5($password.$member->salt)) $update['password'] = $password;

                if(empty($update)){
                    return $this->successJson([],'修改成功');
                }
                $member->update($update);
                $op = '修改';
            }else{
                $salt = Str::random(8);
                $id = Members::insertGetId([
                    'nickname'=>$nick,
                    'phone'=>$phone,
                    'password'=>md5($password.$salt),
                    'salt'=>$salt,
                    'status'=>$status,
                    'create_at'=>$_SERVER['REQUEST_TIME']
                ]);
                $op = '添加';
            }
            $mr = [];
            foreach ($role as $v) {
                $mr[] = ['member_id'=>$id, 'role_id'=>$v, 'create_at'=>$_SERVER['REQUEST_TIME']];
            }
            MemberRoleRelation::where('member_id',$id)->delete();
            MemberRoleRelation::insert($mr);
            DB::commit();
            return $this->successJson([],$op.'成功');
        }catch (\Exception $e) {
            DB::rollBack();
            Tools::log(base_path('storage/logs/'), '添加|修改成员错误：'.$e->getMessage());
            return $this->failJson('提交识别');
        }
    }

    // 成员列表
    public function memberList()
    {
        $page = intval($this->params['page'] ?? 1);
        $size = intval($this->params['size'] ?? 50);
        $page = $page > 0 ? ($page-1) * $size : 0;
        $keyword = trim($this->params['keyword'] ?? '');

        $where = [['deleted','=',CommonEnums::NORMAL]];
        if($keyword){
            $where[] = ['nickname', 'like', '%'.$keyword.'%'];
        }
        $total = Members::where($where)->count();
        $list = Members::where($where)->with('roles')
            ->select(['id','nickname','phone','status','last_login_at','create_at'])
            ->offset($page)->limit($size)
            ->get();

        if(!$list->isEmpty()){
            $rid = [];
            foreach ($list as $v) {
                $v->role_id = $v->roles->pluck('role_id')->toArray();
                $rid = array_merge($rid,$v->role_id);
            }
            sort($rid);
            $rid = array_unique($rid);
            $roles = Roles::whereIn('id',$rid)->where(['status'=>0,'deleted'=>CommonEnums::NORMAL])->select(['id','name'])->get();

            foreach ($list as $l) {
                $_r = [];
                foreach ($roles as $r) {
                    if(in_array($r->id, $l->role_id)){
                        $_r[] = $r;
                    }
                }
                unset($l->role_id, $l->roles);
                $l->roles = $_r;
            }
        }

        return $this->successJson(['total'=>$total, 'list'=>$list]);
    }

    // 删除成员
    public function delSys()
    {
        $id = intval($this->params['id'] ?? 0);
        $body = trim($this->params['body'] ?? '');
        if(!in_array($body, ['role','member','permission'])){
            return $this->failJson('操作类型错误');
        }
        switch (trim($body)){
            case 'member':
                $result = Members::where(['id'=>$id,'deleted'=>CommonEnums::NORMAL])->select(['id','deleted'])->first();
                break;
            case 'role':
                $result = Roles::where(['id'=>$id,'deleted'=>CommonEnums::NORMAL])->select(['id','deleted'])->first();
                break;
            case 'permission':
                $result = Permissions::where(['id'=>$id,'deleted'=>CommonEnums::NORMAL])->select(['id','deleted'])->first();
                break;
        }
        if(empty($result)){
            return $this->failJson('id错误');
        }

        $result->deleted = CommonEnums::DELETE;
        $res = $result->save();
        return $res ? $this->successJson([],'删除成功') : $this->failJson('删除失败，请重试');
    }
}
