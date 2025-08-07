<?php

namespace App\Handlers;

use App\Database;
use App\Infrastructure\ProductRepositoryInterface;
use App\Models\Category;
use App\Models\Product;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FillProductsAndCategoriesHandler
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $connection = Database::getConnection();
        $connection->beginTransaction();

        $firstCategory = new Category();
        $firstCategory->setTitle("First category");

        $secondCategory = new Category();
        $secondCategory->setTitle("Second category");

        $thirdCategory = new Category();
        $thirdCategory->setTitle("Third category");

        $categories = [
            $firstCategory,
            $secondCategory,
            $thirdCategory
        ];

        foreach ($categories as $category) {
            $stmt = $connection->prepare("SELECT * FROM categories WHERE title = :title");
            $stmt->execute([$category->getTitle()]);
            $result = $stmt->fetchAll();
            if (empty($result)) {
                $stmt = $connection->prepare("INSERT INTO categories (title) VALUES (:title)");
                $stmt->execute([$category->getTitle()]);
                $category->setId($connection->lastInsertId());
            } else {
                $category->setId($result[0]['id']);
            }
        }

        $connection->commit();

        $products = [];
        $j = 1; // loop category id
        for ($i = 1; $i < 10; $i++) {
            $product = new Product();
            $product->setTitle("Product $i");
            $product->setCategoryId($j);

            $product = $this->productRepository->save($product);

            $j++;
            if ($j > 3) {
                $j = 1;
            }

            $products[] = $product;
        }

        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'category_id' => $product->getCategoryId(),
            ];
        }

        return new JsonResponse($result);
    }
}