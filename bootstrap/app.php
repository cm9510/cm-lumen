<?php
require_once __DIR__.'/../vendor/autoload.php';

$dir = dirname(__DIR__);
(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables($dir))->bootstrap();

date_default_timezone_set('PRC');

# Create The Application
$app = new Laravel\Lumen\Application($dir);

$app->withFacades();
$app->withEloquent();

# Register Container Bindings
$app->singleton('Illuminate\Contracts\Debug\ExceptionHandler', 'App\Exceptions\Handler');
$app->singleton('Illuminate\Contracts\Console\Kernel', 'App\Console\Kernel');

$app->singleton('redis', function (){
    $config = config('app.redis');
    return new App\Service\Redis([
        'host'=> $config['host'],
        'port'=> $config['port'],
        'timeout'=> $config['timeout'],
        'password'=> $config['password']
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

return $app;
