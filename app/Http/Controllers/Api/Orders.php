<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class Orders extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list()
    {
        return $this->successJson(['id'=>1,'name'=>'alice']);
    }
}
