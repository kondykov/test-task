<?php

namespace App\Infrastructure\Repositories;

use App\Database;
use App\Infrastructure\OrderRepositoryInterface;
use App\Models\Order;
use DateTime;

class OrderRepository implements OrderRepositoryInterface
{

    function save(Order $order): Order
    {
        $connection = Database::getConnection();
        $connection->beginTransaction();

        $order->setCreatedAt(new DateTime());
        $order->setUpdatedAt(new DateTime());

        $stmt = $connection->prepare("INSERT INTO orders (product_id, created_at, updated_at) VALUES (:product_id, :created_at, :updated_at)");
        $stmt->execute([
            'product_id' => $order->getProductId(),
            'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $order->getUpdatedAt()->format('Y-m-d H:i:s')
        ]);
        $order->setId($connection->lastInsertId());

        $connection->commit();
        return $order;
    }

    function getLastHundred(): array
    {
        $connection = Database::getConnection();
        $connection->beginTransaction();

        $stmt = $connection->prepare("SELECT * FROM orders ORDER BY created_at DESC LIMIT 100");
        $stmt->execute();

        $connection->commit();

        $orders = [];
        foreach ($stmt->fetchAll() as $item) {
            $order = new Order();
            $order->setId($item['id']);
            $order->setCreatedAt(new DateTime($item['created_at']));
            $order->setUpdatedAt(new DateTime($item['updated_at']));
            $order->setProductId($item['product_id']);
            
            $orders[] = $order;
        }
        
        return $orders;
    }
}