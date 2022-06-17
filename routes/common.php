<?php
/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('show_captcha', 'Common@captcha');
$router->get('upload_img', 'Common@uploadImg');
