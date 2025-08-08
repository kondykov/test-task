<?php

use App\ApplicationRouter;
use App\Handlers\FillProductsAndCategoriesHandler;
use App\Handlers\GetProductsAndCategoriesHandler;
use App\Handlers\GetStatisticOnProductSoldHandler;
use App\Handlers\ScriptAlphaHandler;
use App\Handlers\ScriptBetaHandler;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

/** @var ApplicationRouter $router */
return function ($router) {
    $router->get('/', function (ServerRequestInterface $request) {
        
        $page = file_get_contents(__DIR__ . "/../src/App/templates/index.html");
        
        return new HtmlResponse($page);
    });
    
    $router->post('/call-beta-handler', ScriptBetaHandler::class);
    $router->post('/create-order', ScriptAlphaHandler::class);
    $router->get('/fill-products', FillProductsAndCategoriesHandler::class);
    $router->get('/get-statistic', GetStatisticOnProductSoldHandler::class);
    $router->get('/get-products-and-categories', GetProductsAndCategoriesHandler::class);

    // not found - 404
    $router->get('/{any:.*}', function () {
        return new JsonResponse(['data' => false, 404 => 'Not Found']);
    });
};
