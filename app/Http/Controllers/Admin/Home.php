<?php
namespace App\Http\Controllers\Admin;

class Home extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function hello()
    {
        return 'hello lumen';
    }

    public function page()
    {
        return view('home');
    }
}
