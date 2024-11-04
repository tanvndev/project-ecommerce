<?php

// Trong Laravel, Repository Pattern thường được sử dụng để tạo các lớp repository, giúp tách biệt logic của ứng dụng khỏi cơ sở dữ liệu.

namespace App\Repositories\Cart;

use App\Models\CartItem;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Cart\CartItemRepositoryInterface;

class CartItemRepository extends BaseRepository implements CartItemRepositoryInterface
{
    protected $model;

    public function __construct(
        CartItem $model
    ) {
        $this->model = $model;
    }
}
