<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommonEnums;
use Cm\Tool\Tools;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{MemberRoleRelation, Members, PermissionGroup, Roles, Permissions};

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
//        $redirect = trim($this->params['redirect'] ?? '');
        $desc = trim($this->params['desc'] ?? '');
        $routers = $this->params['routers'] ?? [];
        $status = intval($this->params['status'] ?? 0);
        $permissions = $this->params['permissions'] ?? [];

        if (empty($name)) {
            return $this->failJson('请填写角色名称');
        } elseif (empty($key)) {
            return $this->failJson('请填写key');
        } elseif (mb_strlen($desc) > 30) {
            return $this->failJson('角色介绍最多30字');
        } elseif (empty($routers) || !is_array($routers)) {
            return $this->failJson('请填写路由name');
        } elseif (empty($permissions) || !is_array($permissions)) {
            return $this->failJson('请选择权限');
        } elseif (!in_array($status, [0, 1])) {
            return $this->failJson('状态错误');
        }

        $routers = array_map(function ($v) {
            return trim($v);
        }, $routers);
        sort($routers);
        $roles = implode(',', $routers);
        $permissions = array_map(function ($v) {
            if (is_numeric($v) && $v) {
                return $v;
            }
            return null;
        }, $permissions);
        sort($permissions);
        $permissions = implode(',', $permissions);

        if ($id) { // 修改
            $role = Roles::where(['id' => $id, 'deleted_at' => CommonEnums::NORMAL])
                ->select(['id', 'name', 'key', 'desc', 'permission_id', 'routers', 'status', 'updater_id'])
                ->first();
            if (empty($role)) {
                return $this->failJson('角色不存在');
            }
            $update = [];
            if ($role->name != $name) $update['name'] = $name;
            if ($role->key != $key) $update['key'] = $key;
            if ($role->desc != $desc) $update['desc'] = $desc;
            if ($role->permission_id != $permissions) $update['permission_id'] = $permissions;
            if ($role->routers != $roles) $update['routers'] = $routers;
            if ($role->status != $status) $update['status'] = $status;
            if ($update) $update['updater_id'] = $this->aid;
            if (!$update) {
                return $this->successJson([], '修改成功');
            }

            $res = $role->update($update);
            $op = '修改';
        } else {
            $res = Roles::insert([
                'name' => $name,
                'key' => $key,
                'desc' => $desc,
                'permission_id' => $permissions,
                'routers' => $routers,
                'status' => $status,
                'creator_id' => $this->aid,
                'redirect' => '',
                'created_at' => $_SERVER['REQUEST_TIME']
            ]);
            $op = '添加';
        }
        return $res ? $this->successJson([], $op . '成功') : $this->failJson($op . '失败');
    }

    // 角色列表
    public function roleList()
    {
        $page = intval($this->params['page'] ?? 1);
        $size = intval($this->params['size'] ?? 20);
        $page = $page > 0 ? ($page - 1) * $size : 0;
        $keyword = trim($this->params['keyword'] ?? '');

        $where = [['deleted_at', '=', CommonEnums::NORMAL]];
        if ($keyword) {
            $where[] = ['name', 'like', '%' . $keyword . '%'];
        }
        $total = Roles::where($where)->count();
        $list = Roles::where($where)->with(['creator', 'updater'])
            ->select(['id', 'name', 'key', 'desc', 'permission_id', 'routers', 'status', 'creator_id', 'updater_id', 'created_at'])
            ->offset($page)->limit($size)
            ->get();

        if (!$list->isEmpty()) {
            $pid = $list->pluck('permission_id')->toArray();
            $pid = array_unique($pid);
            $pid = explode(',', implode(',', $pid));
            $permissions = Permissions::whereIn('id', $pid)->where(['status' => 0, 'deleted_at' => CommonEnums::NORMAL])->select(['id', 'name'])->get();
            $p = [];
            foreach ($permissions as $pv) {
                $p['_' . $pv->id] = $pv;
            }
            foreach ($list as $v) {
                $v->routers = explode(',', $v->routers);
                $v->permission_id = explode(',', $v->permission_id);
                $_permission = [];
                foreach ($v->permission_id as $_pid) {
                    if (isset($p['_' . $_pid])) {
                        $_permission[] = $p['_' . $_pid];
                    }
                }
                $v->permission = $_permission;
                unset($v->permission_id, $v->creator_id, $v->updater_id);
            }
        }

        return $this->successJson(['total' => $total, 'list' => $list]);
    }

    // 所有角色
    public function rolesAll()
    {
        $list = Roles::where(['status' => 0, 'deleted_at' => CommonEnums::NORMAL])->select(['id', 'name'])->get();
        return $this->successJson($list);
    }

    //添加|修改权限分组
    public function editPermissionGroup()
    {
        $id = trim($this->params['id'] ?? 0);
        $name = trim($this->params['name'] ?? '');
        $key = trim($this->params['key'] ?? '');
        $sort = intval($this->params['sort'] ?? 0);

        if ($id) {
            $group = PermissionGroup::where(['id' => $id, 'deleted_at' => CommonEnums::NORMAL])->select(['id', 'key', 'name', 'sort'])->first();
            if (empty($group)) {
                return $this->failJson('分组不存在');
            }
            $update = [];
            if ($group->name != $name) $update['name'] = $name;
            if ($group->sort != $sort) $update['sort'] = $sort;
            if ($key && $group->key != $key) $update['key'] = $key;
            if (empty($update)) {
                return $this->successJson([], '已修改');
            }
            $res = $group->update($update);
            $op = '修改';
        } else {
            $group = PermissionGroup::create(['name' => $name, 'key' => $key, 'sort' => $sort, 'created_at' => $_SERVER['REQUEST_TIME']]);
            $res = !empty($group);
            $op = '添加';
        }
        $this->saveLog($op . '了权限，' . $op . ($res ? '成功' : '失败'));
        return $res ? $this->successJson($group, $op . '成功') : $this->failJson($op . '失败');
    }

    // 权限分组列表
    public function permissionGroups()
    {
        $lp = trim($this->params['lp'] ?? '');
        $list = PermissionGroup::with('permissions')
            ->where('deleted_at', CommonEnums::NORMAL)
            ->select(['id', 'key', 'name', 'sort'])
            ->orderByDesc('sort')
            ->orderByDesc('id')
            ->get();
        $permissions = [];
        if ($lp == 'load' && isset($list[0]->id)) { //返回第一组的第一页
            $wh = ['group_id' => $list[0]->id, 'deleted_at' => CommonEnums::NORMAL];
            $permissions['total'] = Permissions::where($wh)->count();
            $permissions['list'] = Permissions::where($wh)->with(['creator', 'updater'])
                ->select(['id', 'group_id', 'name', 'url', 'status', 'log', 'creator_id', 'updater_id', 'created_at'])
                ->offset(0)->limit(20)
                ->get();
        }
        foreach ($list as $v) {
            $v->pn = count($v->permissions);
            unset($v->permissions);
        }
        return $this->successJson(['group' => $list, 'permission' => $permissions]);
    }

    // 添加|修改权限
    public function editPermission()
    {
        $id = intval($this->params['id'] ?? 0);
        $name = trim($this->params['name'] ?? '');
        $groupId = trim($this->params['group_id'] ?? '');
        $url = trim($this->params['url'] ?? '');
        $status = intval($this->params['status'] ?? '');
        $log = intval($this->params['log'] ?? '');
        if (!is_numeric($groupId) || !$groupId) {
            return $this->failJson('请选择分组');
        } elseif (!$name) {
            return $this->failJson('权限名不能为空');
        } elseif (!$url) {
            return $this->failJson('url不能为空');
        }

        if ($id) { // 修改
            $permission = Permissions::where(['id' => $id, 'deleted_at' => CommonEnums::NORMAL])
                ->select(['id', 'group_id', 'name', 'url', 'status', 'log', 'creator_id', 'updater_id'])
                ->first();
            if (empty($permission)) {
                return $this->failJson('权限不存在');
            }
            $update = [];
            if ($permission->group_id != $groupId) $update['group_id'] = $groupId;
            if ($permission->name != $name) $update['name'] = $name;
            if ($permission->url != $url) $update['url'] = $url;
            if ($permission->status != $status) $update['status'] = $status;
            if ($permission->log != $log) $update['log'] = $log;
            if ($permission->creator_id != $this->aid) $update['updater_id'] = $this->aid;

            if ($update) {
                $res = $permission->update($update);
            } else {
                return $this->successJson([], '已修改');
            }
            $op = '修改';
        } else {
            $res = Permissions::insert([
                'group_id' => $groupId,
                'name' => $name,
                'url' => $url,
                'status' => $status,
                'log' => $log,
                'creator_id' => $this->aid,
                'created_at' => $_SERVER['REQUEST_TIME']
            ]);
            $op = '添加';
        }
        $this->saveLog($op . '了权限，' . $op . ($res ? '成功' : '失败'));
        return $res ? $this->successJson([], $op . '成功') : $this->failJson($op . '失败');
    }

    // 权限列表
    public function permissionList()
    {
        $page = intval($this->params['page'] ?? 1);
        $size = intval($this->params['size'] ?? 50);
        $page = $page > 0 ? ($page - 1) * $size : 0;
        $keyword = trim($this->params['keyword'] ?? '');
        $groupId = trim($this->params['group_id'] ?? 0);

        $where = [
            ['group_id', '=', $groupId],
            ['deleted_at', '=', CommonEnums::NORMAL]
        ];
        if ($keyword) {
            $where[] = [function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')->orWhere('url', 'like', '%' . $keyword . '%');
            }];
        }
        $total = Permissions::where($where)->count();
        $list = Permissions::where($where)->with(['creator', 'updater'])
            ->select(['id', 'group_id', 'name', 'url', 'status', 'log', 'creator_id', 'updater_id', 'created_at'])
            ->offset($page)->limit($size)
            ->get();

        if (!$list->isEmpty()) {
            foreach ($list as $v) {
                unset($v->creator_id, $v->updater_id);
            }
        }
        return $this->successJson(['total' => $total, 'list' => $list]);
    }

    // 所有权限
    public function permissionAll()
    {
        $list = PermissionGroup::with(['permissions' => function ($query) {
            $query->where('deleted_at', CommonEnums::NORMAL)
                ->selectRaw('id as value,group_id,name as label');
        }])
            ->where('deleted_at', CommonEnums::NORMAL)
            ->selectRaw('id,name')
            ->orderByDesc('sort')->orderByDesc('id')
            ->get();
        $res = [];
        foreach ($list as $v) { //组装成前端要的格式
            $res[] = [
                'value' => $v->id *= -1, //乘-1是为了不与子项的重复
                'label' => $v->name,
                'children' => $v->permissions
            ];
        }
        return $this->successJson($res);
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

        if (!$nick) {
            return $this->failJson('请填写昵称');
        } elseif (!preg_match('/^1[356789]\d{9}$/', $phone)) {
            return $this->failJson('请填写正确的手机号');
        } elseif (strlen($password) != 0 && strlen($password) != 32) {
            return $this->failJson('密码错误');
        } elseif (!is_numeric($status) || !in_array($status, [1, 0])) {
            return $this->failJson('状态错误');
        } elseif (empty($role)) {
            return $this->failJson('请分配角色');
        }
        $role = array_map(function ($r) {
            if (is_numeric($r) && $r) return $r;
        }, $role);

        DB::beginTransaction();
        try {
            if ($id) { // 修改
                $member = Members::where(['id' => $id, 'deleted_at' => CommonEnums::NORMAL])
                    ->select(['id', 'nickname', 'password', 'phone', 'salt', 'status'])
                    ->first();
                if (empty($member)) {
                    return $this->failJson('成员不存在');
                }
                $update = [];
                if ($member->nickname != $nick) $update['nickname'] = $nick;
                if ($member->phone != $phone) $update['phone'] = $phone;
                if ($member->status != $status) $update['status'] = $status;
                if ($password && $member->password != md5($password . $member->salt)) $update['password'] = md5($password . $member->salt);

                if (empty($update)) {
                    return $this->successJson([], '修改成功');
                }
                $member->update($update);
                $op = '修改';
            } else {
                $salt = Str::random(8);
                $id = Members::insertGetId([
                    'nickname' => $nick,
                    'phone' => $phone,
                    'password' => md5($password . $salt),
                    'salt' => $salt,
                    'status' => $status,
                    'created_at' => $_SERVER['REQUEST_TIME']
                ]);
                $op = '添加';
            }
            $mr = [];
            foreach ($role as $v) {
                $mr[] = ['member_id' => $id, 'role_id' => $v, 'created_at' => $_SERVER['REQUEST_TIME']];
            }
            MemberRoleRelation::where('member_id', $id)->delete();
            MemberRoleRelation::insert($mr);
            DB::commit();
            return $this->successJson([], $op . '成功');
        } catch (\Exception $e) {
            DB::rollBack();
            Tools::log(base_path('storage/logs/'), '添加|修改成员错误：' . $e->getMessage());
            return $this->failJson('提交识别');
        }
    }

    // 成员列表
    public function memberList()
    {
        $page = intval($this->params['page'] ?? 1);
        $size = intval($this->params['size'] ?? 30);
        $page = $page > 0 ? ($page - 1) * $size : 0;
        $keyword = trim($this->params['keyword'] ?? '');

        $where = [['deleted_at', '=', CommonEnums::NORMAL]];
        if ($keyword) {
            $where[] = [function ($query) use ($keyword) {
                $query->where('nickname', 'like', '%' . $keyword . '%')->orWhere('phone', 'like', '%' . $keyword . '%');
            }];
        }
        $total = Members::where($where)->count();
        $list = Members::where($where)->with('roleIds')
            ->select(['id', 'nickname', 'phone', 'status', 'last_login_at', 'created_at'])
            ->offset($page)->limit($size)
            ->get();

        if (!$list->isEmpty()) {
            $rid = [];
            foreach ($list as $v) {
                $v->role_id = $v->roleIds->pluck('role_id')->toArray();
                $rid = array_merge($rid, $v->role_id);
            }
            sort($rid);
            $rid = array_unique($rid);
            $roles = Roles::whereIn('id', $rid)->where(['status' => 0, 'deleted_at' => CommonEnums::NORMAL])->select(['id', 'name'])->get();

            foreach ($list as $l) {
                $_r = [];
                foreach ($roles as $r) {
                    if (in_array($r->id, $l->role_id)) {
                        $_r[] = $r;
                    }
                }
                unset($l->role_id, $l->roleIds);
                $l->roles = $_r;
            }
        }
        return $this->successJson(['total' => $total, 'list' => $list]);
    }

    // 删除成员|角色|权限
    public function delSys()
    {
        $id = intval($this->params['id'] ?? 0);
        $body = trim($this->params['body'] ?? '');
        switch ($body) {
            case 'member':
                $txt = '成员';
                $result = Members::where(['id' => $id, 'deleted_at' => CommonEnums::NORMAL])->select(['id', 'deleted_at'])->first();
                break;
            case 'role':
                $txt = '角色';
                $result = Roles::where(['id' => $id, 'deleted_at' => CommonEnums::NORMAL])->select(['id', 'deleted_at'])->first();
                break;
            case 'permission':
                $txt = '权限';
                $result = Permissions::where(['id' => $id, 'deleted_at' => CommonEnums::NORMAL])->select(['id', 'deleted_at'])->first();
                break;
            case 'permission_group':
                $txt = '权限分组';
                $result = PermissionGroup::where(['id' => $id, 'deleted_at' => CommonEnums::NORMAL])->select(['id', 'deleted_at'])->first();
                break;
            default:
                return $this->failJson('操作类型错误');
        }
        if (empty($result)) {
            return $this->failJson('id错误');
        }

        $result->deleted_at = $_SERVER['REQUEST_TIME'];
        $res = $result->save();
        $this->saveLog('删除了' . $txt . '，删除' . ($res ? '成功' : '失败'));
        return $res ? $this->successJson([], '删除成功') : $this->failJson('删除失败，请重试');
    }

    // 成员日志列表
    public function memberLogs()
    {
        $keyword = trim($this->params['keyword'] ?? '');
        $range = trim($this->params['range'] ?? '');
        $page = intval($this->params['page'] ?? 1);
        $size = intval($this->params['size'] ?? 30);
        $page = $page > 0 ? ($page - 1) * $size : 0;

        $where = [];
        if ($keyword) $where[] = [function ($query) use ($keyword) {
            $query->where('m.nickname', 'like', '%' . $keyword . '%')->orWhere('m.phone', 'like', '%' . $keyword . '%');
        }];
        if ($range) {
            $range = explode(',', $range);
            $t1 = strtotime($range[0] ?? '');
            $t2 = strtotime($range[1] ?? '');
            if ($t1 && $t2) {
                $where[] = [function ($query) use ($t1, $t2) {
                    $query->whereBetween('l.created_at', [$t1, $t2]);
                }];
            }
        }

        $total = DB::table('cm_member_log as l')
            ->join('cm_members as m', 'm.id', '=', 'l.member_id', 'left')
            ->where($where)
            ->count();
        $list = DB::table('cm_member_log as l')
            ->join('cm_members as m', 'm.id', '=', 'l.member_id', 'left')
            ->select(['l.member_id', 'l.title', 'l.detail', 'l.ip', 'l.created_at', 'm.nickname'])
            ->where($where)
            ->orderBYDesc('l.id')
            ->offset($page)->limit($size)
            ->get();

        return $this->successJson(['total' => $total, 'list' => $list]);
    }
}
