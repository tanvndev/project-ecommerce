<?php

// Trong Laravel, Repository Pattern thường được sử dụng để tạo các lớp repository, giúp tách biệt logic của ứng dụng khỏi cơ sở dữ liệu.

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\Product\ProductRepositoryInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(
        Product $model
    ) {
        $this->model = $model;
    }

    public function pagination(
        $column = ['*'],
        $condition = [],
        $perPage = 10,
        $orderBy = ['id' => 'DESC'],
        $join = [],
        $relations = [],
        $groupBy = [],
        $withWhereHas = [],
        $rawQuery = [],
    ) {
        $query = $this->model->select($column);
        $query->search($condition['search'] ?? null, $condition['searchFields'] ?? null)
            ->publish($condition['publish'] ?? null)
            ->customWhere($condition['where'] ?? null)
            ->customWhereRaw($rawQuery['whereRaw'] ?? null)
            ->relation($relations ?? null)
            ->relationCount($relations ?? null)
            ->customJoin($join ?? null)
            ->customGroupBy($groupBy ?? null)
            ->customOrderBy($orderBy ?? null);

        if ( ! empty($withWhereHas)) {
            // Apply constraints to eager-loaded relationships
            foreach ($withWhereHas as $relation => $callback) {
                $query->whereHas($relation, $callback);
            }
        }

        if ( ! empty($condition['archive'] ?? null) && $condition['archive'] == true) {
            $query->onlyTrashed();
        }

        //Phương thức withQueryString() trong Laravel được sử dụng để giữ nguyên các tham số truy vấn
        return $query->paginate($perPage)->withQueryString();
    }

    public function findProductLock($id)
    {
        $product = Product::query()
            ->whereIn('id', $id)
            ->whereHas('variants', function ($query) {
                $query->where('is_used', true);
            })->first();

        return ( ! empty($product) || $product != null) ? true : false;
    }
}
