<?php

use App\ApplicationRouter;
use App\Handlers\FillProductsAndCategoriesHandler;
use App\Handlers\ScriptAlphaHandler;
use App\Handlers\ScriptBetaHandler;
use Laminas\Diactoros\Response\JsonResponse;

/** @var ApplicationRouter $router */
return function ($router) {
    $router->post('/call-beta-handler', ScriptBetaHandler::class);
    $router->post('/create-order', ScriptAlphaHandler::class);
    $router->get('/fill-products', FillProductsAndCategoriesHandler::class);

    // not found - 404
    $router->get('/{any:.*}', function () {
        return new JsonResponse(['data' => false, 404 => 'Not Found']);
    });
};
