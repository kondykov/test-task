<?php

namespace App\Handlers;

use App\Infrastructure\CategoryRepositoryInterface;
use App\Infrastructure\ProductRepositoryInterface;
use App\Models\Category;
use App\Models\Product;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class GetProductsAndCategoriesHandler
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private ProductRepositoryInterface  $productRepository,
    )
    {
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $products = $this->productRepository->getAll();
        $categories = $this->categoryRepository->getAll();

        $jsonProducts = [];
        /** @var Product $product */
        foreach ($products as $product) {
            $jsonProducts[] = [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'category_id' => $product->getCategoryId(),
            ];
        }

        $jsonCategories = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            $jsonCategories[] = [
                'id' => $category->getId(),
                'title' => $category->getTitle(),
            ];
        }

        return new JsonResponse([
            'categories' => $jsonCategories,
            'products' => $jsonProducts,
        ]);
    }
}