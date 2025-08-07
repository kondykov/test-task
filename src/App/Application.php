<?php

namespace App;

readonly class Application
{
    public function __construct(
        private ApplicationRouter $router = new ApplicationRouter(),
    )
    {
    }

    public function run()
    {
        $routes = require __DIR__ . '/../config/routes.php';

        $this->router->useRoutes($routes);
        $this->router->run();
    }
}