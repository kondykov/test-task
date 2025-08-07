<?php

namespace App;

use App\Handlers\ScriptAlphaHandler;
use App\Handlers\ScriptBetaHandler;
use App\Infrastructure\OrderRepositoryInterface;
use App\Infrastructure\ProductRepositoryInterface;
use App\Infrastructure\Repositories\OrderRepository;
use App\Infrastructure\Repositories\ProductRepository;
use League\Container\Container;
use League\Container\ReflectionContainer;

readonly class Application
{
    private ApplicationRouter $router;

    public function __construct()
    {
        $container = new Container();
        $container->delegate(new ReflectionContainer());

        $container->add(ProductRepositoryInterface::class, ProductRepository::class);
        $container->add(OrderRepositoryInterface::class, OrderRepository::class);

        $container->add(ScriptAlphaHandler::class)
            ->addArgument($container->get(ProductRepositoryInterface::class))
            ->addArgument($container->get(OrderRepositoryInterface::class));

        $container->add(ScriptBetaHandler::class)
            ->addArgument($container->get(ScriptAlphaHandler::class))
            ->addArgument($container->get(ProductRepositoryInterface::class));


        $this->router = new ApplicationRouter($container);
    }

    public function run()
    {
        $routes = require __DIR__ . '/../../config/routes.php';

        $this->router->useRoutes($routes);
        $this->router->run();
    }
}