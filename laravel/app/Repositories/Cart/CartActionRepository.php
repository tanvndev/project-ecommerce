<?php

// Trong Laravel, Repository Pattern thường được sử dụng để tạo các lớp repository, giúp tách biệt logic của ứng dụng khỏi cơ sở dữ liệu.

namespace App\Repositories\Cart;

use App\Models\CartAction;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Cart\CartActionRepositoryInterface;

class CartActionRepository extends BaseRepository implements CartActionRepositoryInterface
{
    protected $model;

    public function __construct(
        CartAction $model
    ) {
        $this->model = $model;
    }
}
