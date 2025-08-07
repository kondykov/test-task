<?php

namespace App\Infrastructure;

use App\Models\Product;

interface ProductRepositoryInterface
{
    function save(Product $product): Product;
    function getByCategoryId(int $categoryId): array;
    function getById(int $id): Product;
}