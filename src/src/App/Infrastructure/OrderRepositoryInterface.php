<?php

namespace App\Infrastructure;

use App\Models\Order;

interface OrderRepositoryInterface
{
    function save(Order $order): Order;
}