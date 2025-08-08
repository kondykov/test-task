<?php

namespace App\Infrastructure;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    function save(Category $category): Category;
    function findById(int $id): ?Category;
    function findByTitle(string $title): ?Category;
    function getAll(): array;
}