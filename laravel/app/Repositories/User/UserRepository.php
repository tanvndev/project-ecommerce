<?php

// Trong Laravel, Repository Pattern thường được sử dụng để tạo các lớp repository, giúp tách biệt logic của ứng dụng khỏi cơ sở dữ liệu.

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\User\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(
        User $model
    ) {
        $this->model = $model;
    }

    public function findChatList(
        array $conditions = [],
        array $column = ['*'],
        array $relation = [],
        ?string $searchTerm = '',
        ?int $pageSize = 15,
    ) {
        $query = $this->model->select($column);

        if ( ! empty($relation)) {
            $query->relation($relation);
        }

        $query->customWhere($conditions);

        if ( ! empty($whereInParams)) {
            $query->whereIn($whereInParams['field'], $whereInParams['value']);
        }
        if ( ! empty($searchTerm)) {
            $query->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('fullname', 'like', '%' . $searchTerm . '%')
                        ->orWhere('email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        return $query->paginate($pageSize);
    }
}
