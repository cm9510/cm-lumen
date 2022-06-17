<?php
namespace App\Http\Controllers\Admin;

use App\Enums\CommonEnums;
use App\Http\Controllers\Controller;
use App\Models\{Members, MemberRoleRelation};

class Login extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // 登入
    public function login()
    {
        $account = trim($this->params['account'] ?? '');
        $password = trim($this->params['password'] ?? '');
        $code = trim($this->params['captcha'] ?? '');

        if(empty($account) || empty($password)){
            return $this->failJson('账号密码不能为空');
        }
        $code = app('redis')->getKey('captcha_admin_'.$code);
        if(empty($code)){
            return $this->failJson('验证码错误');
        }
        app('redis')->delKey('captcha_admin_'.$code);

        $member = Members::where(['phone'=>$account,'deleted'=>CommonEnums::NORMAL])
            ->select(['id','nickname','password','salt','status','last_login_at'])
            ->first();
        if(empty($member)){
            return $this->failJson('用户不存在');
        }elseif ($member->status == 1){
            return $this->failJson('账号已被禁用');
        }elseif ($member->password != md5($password.$member->salt)){
            return $this->failJson('密码错误');
        }

        // 更新最近登入时间
        $member->update(['last_login_at'=>$_SERVER['REQUEST_TIME']]);

        $result = [
            'nickname'=>$member->nickname,
            'roles'=>[]
        ];
        // 查出角色&权限
        $roles = MemberRoleRelation::where('admin_id', $member->id)->with('roles')->get();
        if(!$roles->isEmpty()){
            $result['roles'] = $roles->pluck('roles')->pluck('roles');
        }

        // 存入缓存
        $arr = [
            'u'=> $member->id,
            's'=> md5($member->salt.$_SERVER['REQUEST_TIME'])
        ];
        $str = base64_encode(json_encode($arr,JSON_UNESCAPED_UNICODE));
        app('redis')->setKey('admin_token_'.$member->id, $arr['s'], 259200); // 3天有效期
        $result['token'] = $str;

        return $this->successJson($result,'欢迎登入：'.$member->nickname);
    }
}
