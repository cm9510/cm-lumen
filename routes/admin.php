<?php
/** @var \Laravel\Lumen\Routing\Router $router */

$router->post('login','Login@login');

$router->post('add_permission','System@editPermission');
$router->get('permission_list','System@permissionList');
$router->get('permission_all','System@permissionAll');
$router->post('add_role','System@editRole');
$router->get('role_list','System@roleList');
$router->get('role_all','System@rolesAll');
$router->post('add_member','System@editMember');
$router->get('members_list','System@memberList');
$router->post('del_sys','System@delSys');

