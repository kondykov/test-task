<?php

/** @var ApplicationRouter $router */

use App\ApplicationRouter;
use App\Handlers\FillProductsAndCategoriesHandler;
use App\Handlers\ScriptAHandler;
use Laminas\Diactoros\Response\JsonResponse;

return function ($router) {
    $router->get('/', ScriptAHandler::class);
    $router->post('/create-order', ScriptAHandler::class);
    $router->get('/fill-products', FillProductsAndCategoriesHandler::class);

    // not found - 404
    $router->get('/{any:.*}', function () {
        return new JsonResponse(['data' => false, 404 => 'Not Found']);
    });
};
