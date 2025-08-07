<?php

namespace App\Models;

class Category
{
    public function __construct(
        private ?int $id = null,
        private ?string $title = null,
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

    public function setId(?int $id): Category
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}