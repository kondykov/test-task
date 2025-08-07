<?php

namespace App\Models;

class ProductCategory
{
    public function __construct(
        public int $product_id,
        public int $category_id,
    )
    {
    }
}