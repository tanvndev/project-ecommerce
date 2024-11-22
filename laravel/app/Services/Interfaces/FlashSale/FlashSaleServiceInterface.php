<?php

namespace App\Services\Interfaces\FlashSale;

interface FlashSaleServiceInterface
{
    public function paginate();

    public function findById($id);

    public function store(array $data);

    public function update($id, array $data);

    public function getFlashSale();
}
