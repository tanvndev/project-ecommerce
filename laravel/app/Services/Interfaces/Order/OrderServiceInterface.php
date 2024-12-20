<?php

namespace App\Services\Interfaces\Order;

interface OrderServiceInterface
{
    public function paginate();

    public function create();

    public function update(string $id);

    public function updatePayment(string $id, array $payload);

    public function getOrder(string $orderCode);

    public function getOrderUserByCode(string $orderCode);

    public function getOrderByUser();

    public function updateStatusOrderToCompleted(string $id);

    public function updateStatusOrderToCancelled(string $id);

    public function createNewOrder();

    public function printAndDownloadOrderByCode(string $code);
}
