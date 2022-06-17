<?php
namespace App\Http\Controllers;

use Gregwar\Captcha\CaptchaBuilder;

class Common extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // 生成验证码
    public function captcha()
    {
        $key = trim($this->params['kw'] ?? 'k');
        if(!in_array($key,['admin',''])){
            return $this->failJson('错误的标识');
        }
        $builder = new CaptchaBuilder;
        $builder->build();
        $phrase = strtolower($builder->getPhrase());
        app('redis')->setKey('captcha_'.$key.'_'.$phrase, $_SERVER['REQUEST_TIME'], 120);

        return $this->successJson(['src'=> $builder->inline()]);
    }


    // 上传图片
    public function uploadImg()
    {
        $attach = $this->params['attach'] ?? '';
        $img = app('request')->file('img');
        if($img->getError() == 0){
            $newName = md5($_SERVER['REQUEST_TIME'] . random_bytes(5));
            $newName .= '.'.$img->getClientOriginalExtension();
            $img->move(base_path('public/upload_tmp'), $newName);

            return $this->successJson([
                'src'=> $newName,
                'link'=> url() .'/upload_tmp/'. $newName,
                'size'=> $img->getSize(),
                'attach'=> trim($attach)
            ]);
        }
        return $this->failJson('上传错误，请重试');
    }
}
