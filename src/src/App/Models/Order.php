<?php

namespace App\Models;

use DateTime;

class Order
{    
    public function __construct(
        private ?int $id = null,
        private ?int $productId = null,
        private ?DateTime $createdAt = null,
        private ?DateTime $updatedAt = null,
    )
    {
    }
    
    public function setId(?int $id): Order
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): void
    {
        $this->productId = $productId;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}