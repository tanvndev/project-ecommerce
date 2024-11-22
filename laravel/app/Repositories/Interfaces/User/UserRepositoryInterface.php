<?php

namespace App\Repositories\Interfaces\User;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findChatList(
        array $conditions = [],
        array $column = ['*'],
        array $relation = [],
        ?string $searchTerm = '',
        ?int $pageSize = 15,
    );
}
