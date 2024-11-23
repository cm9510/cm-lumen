<?php
namespace App\Http\Controllers\Admin;

use App\Enums\CommonEnums;
use App\Http\Controllers\Controller;
use App\Models\{MemberLog,Permissions};
use Illuminate\Support\Facades\DB;

class Base extends Controller
{
    protected $aid = 0; // 成员id
    protected $logId = 0; // 日志id，用于修改日志内容

    public function __construct()
    {
        parent::__construct();

        $token = $this->headers['token'] ?? '';
        $token = json_decode(base64_decode($token),true);
        if(!isset($token['u'])){
            $this->exitError('请先登入');
        }
        $st = app('redis')->getKey('admin_token_'.$token['u']);
        if($token['s'] != $st){
            $this->exitError('登入期限已过期，请重新登入');
        }

        $this->aid = (int)$token['u'];

        $this->checkPermission();
    }

    // 校验接口权限
    protected function checkPermission()
    {
        // 白名单
        $white = [
            '/a/logout','/a/user_info','/a/change_info','/a/user_logs',
        ];
        $path = '/'.request()->path();

        if(!in_array($path, $white)){ //不在白名单里，查角色关系
            $pid = DB::table('cm_role_member as mr')
                ->join('cm_roles as r','r.id','=','mr.role_id','left')
                ->select(DB::raw('r.permission_id'))
                ->get()->toArray();
            $pid = array_column($pid,'permission_id');
            $pid = explode(',',implode(',',$pid));
            $pid = array_unique($pid);

            $permission = Permissions::whereIn('id',$pid)->where(['url'=>$path,'deleted_at'=>CommonEnums::NORMAL])->select(['name','log'])->first();
            if(empty($permission)){
                $this->exitError('无权限访问：['.$path.']');
            }
            if ($permission->log == 1){
                $this->logId = $this->writeMemberLog($this->aid,'操作了【'.$permission->name.'】');
            }
        }
    }

    // 更新成员日志内容
    protected function saveLog(string $remark)
    {
        if ($remark){
            MemberLog::where('id',$this->logId)->update(['detail'=>$remark]);
        }
    }
}
