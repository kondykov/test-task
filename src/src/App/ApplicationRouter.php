<?php

namespace App;

use App\Middleware\ExceptionHandlerMiddleware;
use InvalidArgumentException;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Container\Container;
use League\Route\Router as LeagueRoute;
use League\Route\Strategy\ApplicationStrategy;

class ApplicationRouter
{
    private LeagueRoute $router;
    private $request;
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->router = new LeagueRoute();
        $this->request = ServerRequestFactory::fromGlobals(
            $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
        );

        $strategy = (new ApplicationStrategy())->setContainer($this->container);
        $this->router->setStrategy($strategy);
        
        $this->router->middleware(new ExceptionHandlerMiddleware());
    }

    public function get(string $path, $handler): self
    {
        $this->router->map('GET', $path, $this->resolveHandler($handler));
        return $this;
    }

    public function post(string $path, $handler): self
    {
        $this->router->map('POST', $path, $this->resolveHandler($handler));
        return $this;
    }

    public function useRoutes(callable $callback): self
    {
        $callback($this);
        return $this;
    }

    public function run(): void
    {
        $response = $this->router->dispatch($this->request);
        (new SapiEmitter())->emit($response);
    }

    private function resolveHandler($handler): callable
    {
        if (is_callable($handler)) {
            return $handler;
        }

        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$controller, $method] = explode('@', $handler);

            return function (...$params) use ($controller, $method) {
                $instance = $this->container->get($controller);
                return $instance->$method(...$params);
            };
        }

        if (is_string($handler) && class_exists($handler)) {
            return function (...$params) use ($handler) {
                $instance = $this->container->get($handler);
                return $instance(...$params);
            };
        }

        throw new InvalidArgumentException('Invalid route handler');
    }
}