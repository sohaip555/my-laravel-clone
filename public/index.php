<?php

declare(strict_types = 1);

use App\App;
use App\Config;
use App\Container;
use App\Controllers\GeneratorExampleController;
use App\Controllers\HomeController;
use App\Router;

require_once __DIR__ . '/../vendor/autoload.php';
const STORAGE_PATH = __DIR__ . '/../storage';
const VIEW_PATH = __DIR__ . '/../views';


Dotenv\Dotenv::createImmutable(dirname(__DIR__))
    ->load();

$container = new Container();
$router    = new Router($container);

$router->registerRouteFromAttribute([
    HomeController::class,
    GeneratorExampleController::class,
]);


echo "<pre>";
print_r($router->routes());
echo "</pre>";

(new App(
    $container,
    $router,
    ['uri' => $_SERVER['REQUEST_URI'], 'method' => $_SERVER['REQUEST_METHOD']],
    new Config($_ENV)
))->run();
