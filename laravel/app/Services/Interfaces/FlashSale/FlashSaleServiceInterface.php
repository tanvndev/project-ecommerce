<?php

namespace App\Services\Interfaces\FlashSale;

interface FlashSaleServiceInterface
{

    public function paginate();
    public function getAll();
    public function findById($id);
    public function store(array $data);
    public function update($id, array $data);
}
