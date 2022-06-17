<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class Base extends Controller
{
    protected $aid=0;

    public function __construct()
    {
        parent::__construct();

//        $token = $this->headers['token'] ?? '';
//        $token = json_decode(base64_decode($token),true);
//        if(!isset($token['u'])){
//            $this->exitError('请先登入');
//        }
//        $st = app('redis')->getKey('admin_token_'.$token['u']);
//        if($token['s'] != $st){
//            $this->exitError('登入期限已过期，请重新登入');
//        }
//
//        $this->aid = (int)$token['u'];
    }
}
