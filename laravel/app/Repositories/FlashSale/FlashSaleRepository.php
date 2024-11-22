<?php

// Trong Laravel, Repository Pattern thường được sử dụng để tạo các lớp repository, giúp tách biệt logic của ứng dụng khỏi cơ sở dữ liệu.

namespace App\Repositories\FlashSale;

use App\Models\FlashSale;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\FlashSale\FlashSaleRepositoryInterface;

class FlashSaleRepository extends BaseRepository implements FlashSaleRepositoryInterface
{
    protected $model;

    public function __construct(
        FlashSale $model
    ) {
        $this->model = $model;
    }
}
