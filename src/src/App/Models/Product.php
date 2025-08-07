<?php

namespace App\Models;

class Product
{
    public function __construct(
        private ?int $id = null,
        private ?string $title = null,
        private ?int $categoryId = null,
    )
    {
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setId(?int $id): Product
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }
}