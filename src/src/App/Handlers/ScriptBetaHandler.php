<?php

namespace App\Handlers;

use App\Infrastructure\ProductRepositoryInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Stream;

class ScriptBetaHandler
{
    public function __construct(
        private ScriptAlphaHandler $scriptAHandler,
    )
    {
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $success = [];
        
        $parsedBody = json_decode($request->getBody()->getContents());
        $steps = 100;

        if (isset($parsedBody->steps)) {
            try {
                $steps = (int)$parsedBody->steps;
            } catch (\Throwable $e) {
                return new JsonResponse([
                    'error' => 'Передано недопустимое значение количества итераций (steps)'
                ], 422);
            }
        }

        for ($i = 0; $i < $steps; $i++) {
            $modifiedRequest = $this->createRequestWithBody(
                $request,
                ['product_id' => random_int(1, 100)]
            );

            try {
                $result = $this->scriptAHandler->__invoke($modifiedRequest);
                $success[] = $result->getBody()->getContents();
            } catch (\Exception $e) {
                $success[] = 'Exception: ' . $e->getMessage();
            }
        }

        return new JsonResponse([
            'success' => count($success),
            'data' => $success,
        ]);
    }

    private function createRequestWithBody(
        ServerRequestInterface $originalRequest,
        array                  $bodyData
    ): ServerRequestInterface
    {
        $stream = new Stream('php://temp', 'wb+');
        $stream->write(json_encode($bodyData));
        $stream->rewind();

        return $originalRequest
            ->withBody($stream)
            ->withHeader('Content-Type', 'application/json');
    }
}