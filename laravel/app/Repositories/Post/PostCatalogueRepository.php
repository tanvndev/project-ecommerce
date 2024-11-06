<?php

// Trong Laravel, Repository Pattern thường được sử dụng để tạo các lớp repository, giúp tách biệt logic của ứng dụng khỏi cơ sở dữ liệu.

namespace App\Repositories\Post;

use App\Models\PostCatalogue;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Post\PostCatalogueRepositoryInterface;

class PostCatalogueRepository extends BaseRepository implements PostCatalogueRepositoryInterface
{
    protected $model;

    public function __construct(
        PostCatalogue $model
    ) {
        $this->model = $model;
    }
}
