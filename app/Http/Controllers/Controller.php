<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    # request header array
    protected $headers = [];

    # request params array
    protected $params = [];


    protected function __construct()
    {
        $this->params = Request::all();
        $this->headers = array_map(function ($v){
            return $v[0];
        }, Request::header());
    }

    /**
     * success return
     * @param mixed|array $data
     * @param string $msg
     * @return JsonResponse
     */
    public function successJson($data = [], string $msg = 'success'): JsonResponse
    {
        return new JsonResponse(['code'=> 100, 'msg'=> $msg, 'data'=> $data]);
    }

    /**
     * fail return
     * @param string $msg
     * @param object|array $data
     * @return JsonResponse
     */
    public function failJson(string $msg = 'failed', $data = []): JsonResponse
    {
        return new JsonResponse(['code'=> 101, 'msg'=> $msg, 'data'=> $data]);
    }

    /**
     * exit
     * @param string $msg
     * @param int $code
     */
    protected function exitError(string $msg = 'failed', int $code = 101): void
    {
        header('Content-Type:application/json;charset=utf-8');
        exit(json_encode(['code'=> $code, 'msg'=> $msg],JSON_UNESCAPED_UNICODE));
    }

}
