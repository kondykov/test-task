<?php

namespace App;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router as LeagueRoute;
use League\Route\Strategy\ApplicationStrategy;

class ApplicationRouter
{
    private $router;
    private $request;

    public function __construct()
    {
        $this->router = new LeagueRoute();
        $this->request = ServerRequestFactory::fromGlobals(
            $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
        );
        $this->router->setStrategy(new ApplicationStrategy());
    }

    public function get($path, $callback)
    {
        $this->router->map('GET', $path, $callback($this->request));
    }

    public function post($path, $callback)
    {
        $this->router->map('POST', $path, $callback($this->request));
    }
    
    public function useRoutes(callable $callback)
    {
        $callback($this->router);
        return $this;
    }

    public function run(): void
    {
        $response = $this->router->dispatch($this->request);
        (new SapiEmitter())->emit($response);
    }
}