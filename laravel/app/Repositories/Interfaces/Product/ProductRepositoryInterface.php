<?php

namespace App\Repositories\Interfaces\Product;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function findProductLock($id);
}
