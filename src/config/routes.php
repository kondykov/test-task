<?php

/** @var \App\ApplicationRouter $router */

use Laminas\Diactoros\Response\JsonResponse;

return function ($router) {
    $router->get('/', function ($request) {        
        return new JsonResponse(
            [
                'data' => [],
            ]
        );
    });

    // not found - 404
    $router->get('/{any:.*}', function () {
        return new JsonResponse(['data' => false, 404 => 'Not Found']);
    });
};
