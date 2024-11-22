<?php

// Trong Laravel, Repository Pattern thường được sử dụng để tạo các lớp repository, giúp tách biệt logic của ứng dụng khỏi cơ sở dữ liệu.

namespace App\Repositories\Product;

use App\Models\SearchHistory;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Product\SearchHistoryRepositoryInterface;

class SearchHistoryRepository extends BaseRepository implements SearchHistoryRepositoryInterface
{
    protected $model;

    public function __construct(
        SearchHistory $model
    ) {
        $this->model = $model;
    }
}
