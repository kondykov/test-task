<?php

namespace App\Handlers;

use App\Database;
use App\Infrastructure\OrderRepositoryInterface;
use App\Infrastructure\ProductRepositoryInterface;
use App\Models\Order;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class ScriptAHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private OrderRepositoryInterface   $orderRepository,
    )
    {
    }

    public function __invoke(ServerRequestInterface $request): JsonResponse
    {
        $redis = Database::getRedisConnection();

        $isLocked = $redis->get("script_A_is_locked");
        if ($isLocked) {
            return new JsonResponse([
                'data' => 'Script is locked',
            ], 400);
        }

        $redis->set("script_A_is_locked", true);
        $parsedBody = json_decode($request->getBody()->getContents());
        $product = $this->productRepository->getById($parsedBody->product_id);

        $order = new Order();
        $order->setProductId($product->getId());

        $order = $this->orderRepository->save($order);
        
        sleep(1);
        $redis->set("script_A_is_locked", false);
        
        return new JsonResponse(['data' => [
            'id' => $order->getId(),
            'product_id' => $order->getProductId(),
            'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $order->getUpdatedAt()->format('Y-m-d H:i:s')
        ]]);
    }
}