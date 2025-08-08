<?php

namespace App\Handlers;

use App\Database;
use App\Infrastructure\CategoryRepositoryInterface;
use App\Infrastructure\ProductRepositoryInterface;
use App\Models\Category;
use App\Models\Product;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FillProductsAndCategoriesHandler
{
    public function __construct(
        private ProductRepositoryInterface  $productRepository,
        private CategoryRepositoryInterface $categoryRepository,
    )
    {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
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
            $categoryExists = $this->categoryRepository->findByTitle($category->getTitle());
            if (empty($categoryExists)) {
                $this->categoryRepository->save($category);
            } else {
                $category->setId($categoryExists->getId());
            }
        }

        $products = [];
        $j = 1; // loop category id
        for ($i = 1; $i < 150; $i++) {
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

        $categoryExists = [];
        foreach ($products as $product) {
            $categoryExists[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'category_id' => $product->getCategoryId(),
            ];
        }

        return new JsonResponse($categoryExists);
    }
}