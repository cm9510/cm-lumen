<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class Base extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // $this->middleware();
    }
}
