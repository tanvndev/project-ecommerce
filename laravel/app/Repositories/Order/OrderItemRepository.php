<?php

// Trong Laravel, Repository Pattern thường được sử dụng để tạo các lớp repository, giúp tách biệt logic của ứng dụng khỏi cơ sở dữ liệu.

namespace App\Repositories\Order;

use App\Models\OrderItem;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Order\OrderItemRepositoryInterface;

class OrderItemRepository extends BaseRepository implements OrderItemRepositoryInterface
{
    protected $model;

    public function __construct(
        OrderItem $model
    ) {
        $this->model = $model;
    }
}
