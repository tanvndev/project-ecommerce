<?php

namespace App\Traits;

use Exception;

trait QueryScopes
{
    public function scopeSearch($query, $keyword, $fieldSearch = [], $whereHas = [])
    {
        if (!empty($keyword)) {

            if (!empty($fieldSearch)) {
                foreach ($fieldSearch as $field) {
                    $query->orWhere($field, 'LIKE', '%' . $keyword . '%');
                }
            } else {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
            }
        }

        if (!empty($whereHas)) {
            // $whereHas = [
            //     'field' => 'name',
            //     'relation' => 'product_catalogues',
            // ];
            $field = $whereHas['field'];
            $query->orWhereHas($whereHas['relation'], function ($q) use ($field, $keyword) {
                $q->where($field, 'LIKE', '%' . $keyword . '%');
            });
        }

        return $query;
    }

    public function scopePublish($query, $publish)
    {
        if ((!empty($publish)) && $publish != '0') {
            $query->where('publish', $publish);
        }

        return $query;
    }

    public function scopeCustomWhere($query, $conditions = [])
    {
        // where: ['status' => 'active']
        // where với toán tử: ['price' => ['>', 100]]
        // whereIn: ['category_id' => ['in', [1, 2, 3]]]

        if (!empty($conditions) && is_array($conditions)) {
            foreach ($conditions as $column => $value) {
                if (is_array($value)) {
                    if ($value[0] === 'in') {
                        $query->whereIn($column, $value[1]);
                    } else {
                        $operator = $value[0];
                        $val = isset($value[1]) ? $value[1] : null;
                        $query->where($column, $operator, $val);
                    }
                } else {
                    $query->where($column, $value);
                }
            }
        }

        return $query;
    }

    public function scopeCustomWhereRaw($query, $whereRaw = [])
    {
        // $value[0] là câu truy vấn $value[1] là tham số truy vấn
        if (!empty($whereRaw) && is_array($whereRaw)) {
            foreach ($whereRaw as $key => $value) {
                $query->whereRaw($value[0], $value[1]);
            }
        }
        return $query;
    }

    public function scopeRelation($query, $relations = [])
    {
        // ['user_catalogue', '...']
        if (!empty($relations) && is_array($relations)) {
            foreach ($relations as $relation) {
                $query->with($relation);
            }
        }

        return $query;
    }

    public function scopeRelationCount($query, $relations = [])
    {
        // ['user_catalogue', '...']
        if (!empty($relations) && is_array($relations)) {
            foreach ($relations as $relation) {
                $query->withCount($relation);
            }
        }

        return $query;
    }

    public function scopeCustomJoin($query, $join = [])
    {
        // 'table_name_1' => ['constraint1', 'constraint2'],
        // 'brand' => ['brand.id', 'product.brand_id']
        if (!empty($join) && is_array($join)) {
            foreach ($join as $table => $constraints) {
                $query->join($table, ...$constraints);
            }
        }

        return $query;
    }

    public function scopeCustomGroupBy($query, $groupBy = [])
    {
        // 'column1' or
        // ['column1', 'column2']
        if (!empty($groupBy)) {
            if (is_array($groupBy)) {
                foreach ($groupBy as $group) {
                    $query->groupBy($group);
                }
            } else {
                $query->groupBy($groupBy);
            }
        }

        return $query;
    }

    public function scopeCustomOrderBy($query, $orderBy = [])
    {

        // OrderBy
        // [
        //  'name' => 'ASC',
        //  'created_at' => 'DESC'
        // ]
        if (!empty($orderBy) && is_array($orderBy)) {
            foreach ($orderBy as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('id', 'DESC');
        }

        return $query;
    }

    public function scopeFilterDropdown($query, $condition = [])
    {
        if (!empty($condition) && is_array($condition)) {
            foreach ($condition as $column => $value) {
                if ($value != '') {
                    $query->where($column, $value);
                }
            }
        }

        return $query;
    }

    public function scopeCreatedAt($query, $condition = '')
    {
        if (!empty($condition) && $condition != '') {
            $date = explode(' - ', $condition);
            $startAt = convertDateTime($date[0], 'Y-m-d H:i:s');
            $endAt = convertDateTime($date[1], 'Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startAt, $endAt]);
        }

        return $query;
    }

    public function scopeWhereHasRelations($query, array $relationConditions)
    {
        // 'relation_name' => [
        //     ['field', 'operator', 'value'],
        //     'customFunction' => function($q) {
        //
        //     }
        // ]
        foreach ($relationConditions as $relation => $conditions) {
            $query->whereHas($relation, function ($q) use ($conditions) {
                foreach ($conditions as $condition) {
                    if (is_callable($condition)) {
                        // call closure
                        $condition($q);
                    } elseif (count($condition) === 3) {
                        // ['field', 'operator', 'value']
                        $q->where($condition[0], $condition[1], $condition[2]);
                    } elseif (count($condition) === 2) {
                        // ['field', 'value'] '='
                        $q->where($condition[0], '=', $condition[1]);
                    } else {
                        throw new Exception('Error at whereHasRelations', 1);
                    }
                }
            });
        }

        return $query;
    }
}
