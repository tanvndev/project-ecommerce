<?php

namespace App\Services\Interfaces\Product;

interface ProductServiceInterface
{
    public function paginate();

    public function create();

    public function update($id);

    public function destroy($id);

    public function getProductVariants();

    public function updateVariant();

    public function deleteVariant($id);

    public function createAttribute();

    public function updateAttribute(string $productId);

    public function getProduct(string $slug);

    public function getProductReport();
}
