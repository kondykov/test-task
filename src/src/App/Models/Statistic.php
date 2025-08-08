<?php

namespace App\Models;

class Statistic
{
    public function __construct(
        private int       $productId,
        private int       $categoryId,
        private \DateTime $firstSoldDate,
        private \DateTime $lastSoldDate,
        private int       $countSold = 1,
    )
    {
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getFirstSoldDate(): \DateTime
    {
        return $this->firstSoldDate;
    }

    public function setFirstSoldDate(\DateTime $firstSoldDate): void
    {
        $this->firstSoldDate = $firstSoldDate;
    }

    public function getLastSoldDate(): \DateTime
    {
        return $this->lastSoldDate;
    }

    public function setLastSoldDate(\DateTime $lastSoldDate): void
    {
        $this->lastSoldDate = $lastSoldDate;
    }

    public function getCountSold(): int
    {
        return $this->countSold;
    }

    public function incrementCountSold(): void
    {
        $this->countSold++;
    }
}