<?php

namespace App\Repositories\ProductReview;

use App\Models\ProductReview;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\ProductReview\ProductReviewRepositoryInterface;

class ProductReviewRepository extends BaseRepository implements ProductReviewRepositoryInterface
{
    protected $model;

    public function __construct(ProductReview $model)
    {
        $this->model = $model;
    }
}
