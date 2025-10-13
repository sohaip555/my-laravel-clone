<?php

declare(strict_types = 1);

namespace App;

use App\Exceptions\RouteNotFoundException;
use App\Services\PaymentGatewayService;
use App\Services\PaymentGatewayServiceInterface;

class App
{
    private static DB $db;
    protected Container $container;
    protected Router $router;
    protected array $request;
    protected Config $config;

    public function __construct(
        Container $container,
        Router $router,
        array $request,
        Config $config
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->router = $router;
        $this->container = $container;
        static::$db = new DB($config->db ?? []);

        $this->container->set(PaymentGatewayServiceInterface::class, PaymentGatewayService::class);
    }

    public static function db(): DB
    {
        return static::$db;
    }

    public function run(): void
    {
        try {
            echo $this->router->resolve($this->request['uri'], strtolower($this->request['method']));
        } catch (RouteNotFoundException $ex) {
            http_response_code(404);

            echo View::make('error/404');
        }
    }
}
