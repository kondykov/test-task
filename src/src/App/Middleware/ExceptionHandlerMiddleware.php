<?php

namespace App\Middleware;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ExceptionHandlerMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $exception) {
            error_log(sprintf(
                'Exception: %s in %s:%d',
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            ));
            switch ($exception->getCode()) {                
                case 422:
                    return new JsonResponse([
                        'error' => 'Validation Error',
                        'message' => $exception->getMessage(),
                        'code' => $exception->getCode()
                    ], 422);
                default:
                    return new JsonResponse([
                        'error' => 'Internal Server Error',
                        'message' => $exception->getMessage(),
                        'code' => $exception->getCode()
                    ], 500);
            }
        }
    }
}
