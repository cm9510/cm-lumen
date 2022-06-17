<?php
/** @var \Laravel\Lumen\Routing\Router $router */

$router->post('login','Login@login');

$router->post('add_permission','System@editPermission');
$router->get('permission_list','System@permissionList');
$router->get('permission_all','System@permissionAll');
$router->post('del_permission','System@delPermission');
$router->post('add_role','System@editRole');
$router->get('role_list','System@roleList');
$router->post('del_role','System@delRole');

//2SPR0211,2TIR0173,2OXK0209,2MC50218,2TO10216,2SI60203,264M0222,2FTW0215,2EUO0217,21EU0221,2WIY0206,2RV20219,2FVS0204,26UE0198,24590199,28ZW0200,2ZKW0214,2WIA0212,2BQ80208,22KN0213,27C60207,2VTN0196
