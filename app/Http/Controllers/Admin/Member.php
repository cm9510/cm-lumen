<?php
namespace App\Http\Controllers\Admin;

use App\Enums\CommonEnums;
use App\Models\{MemberLog,Members};

class Member extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    // 退出登入
    public function logout()
    {
        app('redis')->delKey('admin_token_'.$this->aid);
        $this->writeMemberLog($this->aid,'退出登入','主动退出后台');
        return $this->successJson();
    }

    // 成员信息
    public function userInfo()
    {
        $info = Members::where(['id'=>$this->aid,'deleted_at'=>CommonEnums::NORMAL])
            ->with(['roleIds'=>function($query){
                $query->with('roles');
            }])
            ->select(['id','nickname','phone','created_at'])->first();

        if (empty($info)){
            return $this->failJson('未查询到用户资料');
        }
        $r = [];
        foreach ($info->roleIds as $v){
            $r[] = $v->roles->name;
        }
        $info->roles = $r;
        $info->phone = hide_phone($info->phone);
        unset($info->roleIds);
        return $this->successJson($info);
    }

    // 修改资料
    public function updateInfo()
    {
        $nickname = trim($this->params['nick'] ?? '');
        $phone = trim($this->params['phone'] ?? '');
        $password = trim($this->params['password'] ?? '');

        if (mb_strlen($nickname) > 12){
            return $this->failJson('昵称不能超过12个字符');
        }elseif ($phone && !preg_match('/^1[3456789]\d{9}$/', $phone)){
            return $this->failJson('请填写正确的手机号');
        }elseif ($password && strlen($password) !== 32){
            return $this->failJson('密码参数错误');
        }

        $info = Members::where('id',$this->aid)->select(['id','nickname','phone','salt','password'])->first();
        if(empty($info)){
            return $this->failJson('当前成员错误');
        }
        $log = [];
        if ($nickname) {
            $info->nickname = $nickname;
            $log[] = '昵称';
        }
        if ($phone) {
            $info->phone = $phone;
            $log[] = '手机号';
        }
        if ($password) {
            $info->password = md5($password, $info->salt);
            $log[] = '密码';
        }
        $info->save();
        $info->phone = hide_phone($info->phone);
        unset($info->id,$info->password,$info->salt);

        $this->writeMemberLog($this->aid,'修改资料','修改字段：'.($log?implode(', ',$log):''));
        return $this->successJson($info,'修改成功');
    }

    //日志
    public function logs()
    {
        $range = trim($this->params['range'] ?? '');
        $page = intval($this->params['page'] ?? 1);
        $size = intval($this->params['size'] ?? 20);
        $page = $page > 0 ? ($page-1) * $size : 0;

        $where = [['member_id','=',$this->aid]];
        if ($range){
            $range = explode(',',$range);
            $t1 = strtotime($range[0] ?? '');
            $t2 = strtotime($range[1] ?? '');
            if ($t1 && $t2){
                $where[] = [function($query) use ($t1,$t2){
                    $query->whereBetween('created_at',[$t1,$t2]);
                }];
            }
        }
        $total = MemberLog::where($where)->count();
        $list = MemberLog::where($where)
            ->select(['title','detail','ip','created_at'])
            ->orderByDesc('id')
            ->offset($page)->limit($size)
            ->get();
        return $this->successJson(['total'=>$total,'list'=>$list]);
    }
}
