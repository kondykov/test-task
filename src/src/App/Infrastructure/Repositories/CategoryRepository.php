<?php

namespace App\Infrastructure\Repositories;

use App\Database;
use App\Infrastructure\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{

    function save(Category $category): Category
    {
        $connection = Database::getConnection();
        $connection->beginTransaction();

        $stmt = $connection->prepare("INSERT INTO categories (title) VALUES (:title)");
        $stmt->execute([$category->getTitle()]);
        $category->setId($connection->lastInsertId());

        $connection->commit();

        return $category;
    }

    function findById(int $id): ?Category
    {
        $connection = Database::getConnection();
        $connection->beginTransaction();

        $stmt = $connection->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        $category = new Category();
        $category->setId($result['id']);
        $category->setTitle($result['title']);

        $connection->commit();

        return $category;
    }

    function getAll(): array
    {
        $connection = Database::getConnection();
        $connection->beginTransaction();

        $stmt = $connection->prepare("SELECT * FROM categories");
        $stmt->execute([]);
        $result = $stmt->fetchAll();

        $categories = [];

        foreach ($result as $item) {
            $category = new Category();
            $category->setId($item['id']);
            $category->setTitle($item['title']);

            $categories[] = $category;
        }

        $connection->commit();

        return $categories;
    }

    function findByTitle(string $title): ?Category
    {
        $connection = Database::getConnection();
        $connection->beginTransaction();

        $stmt = $connection->prepare("SELECT * FROM categories WHERE title = :title LIMIT 1");
        $stmt->execute([$title]);
        $result = $stmt->fetch();

        if (!$result) {
            $connection->rollback();
            return null;
        }

        $category = new Category();
        $category->setId($result['id']);
        $category->setTitle($result['title']);

        $connection->commit();

        return $category;
    }
}