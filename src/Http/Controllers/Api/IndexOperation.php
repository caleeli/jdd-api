<?php

namespace JDD\Api\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use JDD\Api\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class IndexOperation extends BaseOperation
{
    protected $createNewRows = false;
    protected $sort;
    protected $filter;
    protected $perPage;

    public function index($sort, $filter, $perPage)
    {
        $this->sort = $sort;
        $this->filter = $filter;
        $this->perPage = $perPage;
        return $this->execute($this->model, null);
    }

    protected function isBelongsTo(BelongsTo $model, Model $target = null, $data)
    {
        return $model->first();
    }

    protected function isBelongsToMany(BelongsToMany $model, array $targets = null, $data)
    {
        return $this->addSorting($this->addFilter($model))
                ->paginate($this->perPage)->getCollection();
    }

    protected function isHasMany(HasMany $model, array $targets = null, $data)
    {
        return $this->addSorting($this->addFilter($model))
                ->paginate($this->perPage)->getCollection();
    }

    protected function isHasOne(HasOne $model, Model $target = null, $data)
    {
        return $model->first();
    }

    protected function isModel(Model $model, Model $target = null, $data)
    {
        return $model;
    }

    protected function isNull($model, Model $target = null, $data)
    {
        throw new NotFoundException();
    }

    protected function isString($model, Model $target = null, $data)
    {
        $result = $model::select();
        return $this->addSorting($this->addFilter($result))
                ->paginate($this->perPage)->getCollection();
    }

    protected function isArray($model, $target = null, $data)
    {
        return $model;
    }

    protected function isCollection(Collection $model, $target = null, $data)
    {
        return $model;
    }

    /**
     *
     * &filter[]=where,username,=,david
     * @param Builder $select
     * @return Builder
     */
    protected function addFilter($select)
    {
        if (empty($this->filter)) {
            return $select;
        }
        $relFilter = [];
        foreach ($this->filter as $filter) {
            $params = explode(',', $filter);
            $method = array_shift($params);
            $relation = false;
            if (substr($method, 0, 1) === '@') {
                $relation = substr($method, 1);
                $method = array_shift($params);
            }
            foreach ($params as $i => $param) {
                $decoded = json_decode($param);
                $params[$i] = $decoded !== null ? $decoded : $param;
            }
            if ($relation) {
                $relFilter[$relation][] = [$method, $params];
            } else {
                $select = call_user_func_array([$select, $method], $params);
            }
        }
        foreach ($relFilter as $relation => $relations) {
            $select = $select->with([$relation => function ($select) use ($relations) {
                list($method, $params) = $relations;
                call_user_func_array([$select, $method], $params);
            }]);
        }
        return $select;
    }

    /**
     *
     * &sort=name,-date
     * @param Builder $select
     * @return Builder
     */
    protected function addSorting($select)
    {
        if (empty($this->sort)) {
            return $select;
        }
        foreach (explode(',', $this->sort) as $sSort) {
            if (substr($sSort, 0, 1) === '-') {
                $sort = substr($sSort, 1);
                $dir = 'desc';
            } elseif (substr($sSort, 0, 1) === '+') {
                $sort = substr($sSort, 1);
                $dir = 'asc';
            } else {
                $sort = $sSort;
                $dir = 'asc';
            }
            $select->orderBy($sort, $dir);
        }
        return $select;
    }
}
