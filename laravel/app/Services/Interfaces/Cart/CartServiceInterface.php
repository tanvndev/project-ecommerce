<?php

namespace App\Services\Interfaces\Cart;

interface CartServiceInterface
{
    public function getCart();

    public function createOrUpdate($request);

    public function deleteOneItem($id);

    public function cleanCart();

    public function handleSelected($request);

    public function deleteCartSelected();

    public function addPaidProductsToCart($request);

    public function buyNow($request);
}
