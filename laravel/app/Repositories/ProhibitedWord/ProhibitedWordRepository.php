<?php

// Trong Laravel, Repository Pattern thường được sử dụng để tạo các lớp repository, giúp tách biệt logic của ứng dụng khỏi cơ sở dữ liệu.

namespace App\Repositories\ProhibitedWord;

use App\Models\ProhibitedWord;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\ProhibitedWord\ProhibitedWordRepositoryInterface;

class ProhibitedWordRepository extends BaseRepository implements ProhibitedWordRepositoryInterface
{
    protected $model;

    public function __construct(
        ProhibitedWord $model
    ) {
        $this->model = $model;
    }
}
