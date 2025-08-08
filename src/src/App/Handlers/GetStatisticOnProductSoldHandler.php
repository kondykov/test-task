<?php

namespace App\Handlers;

use App\Infrastructure\OrderRepositoryInterface;
use App\Infrastructure\ProductRepositoryInterface;
use App\Models\Order;
use App\Models\Statistic;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class GetStatisticOnProductSoldHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private OrderRepositoryInterface   $orderRepository,
    )
    {
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $orders = $this->orderRepository->getLastHundred();

        $statistics = [];

        /** @var Order $order */
        foreach ($orders as $order) {
            $productId = $order->getProductId();
            $product = $this->productRepository->findById($productId);

            if (isset($statistics[$product->getId()])) {
                /** @var Statistic $statisticItem */
                $statisticItem = $statistics[$product->getId()];

                $statisticItem->incrementCountSold();

                $firstSoldDate = $statisticItem->getFirstSoldDate();
                $lastSoldDate = $statisticItem->getLastSoldDate();
                $orderSoldDate = $order->getCreatedAt();

                if ($orderSoldDate < $firstSoldDate) {
                    $statisticItem->setFirstSoldDate($orderSoldDate);
                }

                if ($orderSoldDate > $lastSoldDate) {
                    $statisticItem->setLastSoldDate($orderSoldDate);
                }
            } else {
                $statisticItem = new Statistic(
                    $product->getId(),
                    $product->getCategoryId(),
                    $order->getCreatedAt(),
                    $order->getCreatedAt(),
                );

                $statistics[$product->getId()] = $statisticItem;
            }
        }

        return new JsonResponse([
            'statistic' => $this->extract($statistics),
        ]);
    }

    private function extract(array $statistics): array
    {
        $result = [];

        /** @var Statistic $statistic */
        foreach ($statistics as $statistic) {
            $different = $statistic->getFirstSoldDate()->diff($statistic->getLastSoldDate());
            
            $result[] = [
                'product_id' => $statistic->getProductId(),
                'different_sold_dates' => $different->format('%a дней, %h:%i:%s'),
                'count_sold' => $statistic->getCountSold(),
            ];
        }

        return $result;
    }
}