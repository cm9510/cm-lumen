<?php
require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(dirname(__DIR__)))->bootstrap();

date_default_timezone_set('PRC');

# Create The Application
$app = new Laravel\Lumen\Application(dirname(__DIR__));

$app->withFacades();
$app->withEloquent();

# Register Container Bindings
$app->singleton('Illuminate\Contracts\Debug\ExceptionHandler', 'App\Exceptions\Handler');
$app->singleton('Illuminate\Contracts\Console\Kernel', 'App\Console\Kernel');

$app->singleton('redis', function (){
    return new App\Service\Redis([
        'host'=> env('REDIS_HOST', '127.0.0.1'),
        'port'=> env('REDIS_PORT', 6379),
        'timeout'=> env('REDIS_TIMEOUT', 30),
        'password'=> env('REDIS_PASSWORD', '')
    ]);
});
# Register Config Files
//$app->configure('app');

# Register Middleware
//$app->middleware([]);
//$app->routeMiddleware([]);

# Register Service Providers
// $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);

# Load The Application Routes
$app->router->group(['namespace' => 'App\Http\Controllers'], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
