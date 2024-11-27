<?php

namespace App\Services\Interfaces\Order;

interface OrderStatusServiceInterface
{
    public function paginate();

    public function create();

    public function update();

    public function cancel();

}
