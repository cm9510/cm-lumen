<?php
namespace App\Http\Controllers;

class Common extends Controller
{
    public function __construct()
    {
        parent::__construct();
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
