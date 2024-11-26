<?php

// Trong Laravel, Repository Pattern thường được sử dụng để tạo các lớp repository, giúp tách biệt logic của ứng dụng khỏi cơ sở dữ liệu.

namespace App\Repositories\Order;

use App\Repositories\BaseRepository;
use App\Models\OrderStatusChangeRequest;
use App\Repositories\Interfaces\Order\OrderStatusRepositoryInterface;


class OrderStatusRepository extends BaseRepository implements OrderStatusRepositoryInterface
{
    public $model;
    public function __construct(
        OrderStatusChangeRequest $model
    ) {
        $this->model = $model;
    }
}
