<?php

namespace App\Infrastructure\Repositories;

use App\Database;
use App\Infrastructure\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    function save(Product $product): Product
    {
        $connection = Database::getConnection();
        $connection->beginTransaction();

        $stmt = $connection->prepare("INSERT INTO products (title) VALUES (:title)");
        $stmt->execute(['title' => $product->getTitle()]);
        $product->setId($connection->lastInsertId());

        $stmt = $connection->prepare("INSERT INTO product_category (product_id, category_id) VALUES (:product_id, :category_id)");
        $stmt->execute([
            'product_id' => $product->getId(),
            'category_id' => $product->getCategoryId()
        ]);

        $connection->commit();
        return $product;
    }

    /**
     * @throws \Exception
     */
    function getById(int $id): Product
    {
        $connection = Database::getConnection();
        $connection->beginTransaction();

        $stmt = $connection->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        
        if (!$result) {
            $connection->rollback();
            throw new \Exception("Продукта с id:$id не существует", 422);
        }

        $product = new Product();
        $product->setId($result['id']);
        $product->setTitle($result['title']);

        $stmt = $connection->prepare("SELECT category_id FROM product_category WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $product->getId()]);
        $categoryId = $stmt->fetchColumn();

        $product->setCategoryId($categoryId);
        $connection->commit();
        return $product;
    }

    function getByCategoryId(int $categoryId): array
    {
        $connection = Database::getConnection();
        $connection->beginTransaction();

        $stmt = $connection->prepare("SELECT * FROM product_category WHERE category_id = :category_id LIMIT 1");
        $stmt->execute(['category_id' => $categoryId]);
        $result = $stmt->fetchAll();

        $products = [];

        foreach ($result as $productArray) {
            $product = new Product();
            $product->setId($productArray['id']);
            $product->setTitle($productArray['title']);

            $products[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'category_id' => $categoryId
            ];
        }

        return $products;
    }
}