<?php

namespace JDD\Api\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use JDD\Api\Exceptions\NotFoundException;

class IndexOperation extends BaseOperation
{
    protected $createNewRows = false;
    protected $sort;
    protected $filter;
    protected $perPage;
    protected $fields;
    protected $count = false;

    public function index($sort, $filter, $perPage, $fields, $count = false)
    {
        $this->sort = $sort;
        $this->filter = $filter;
        $this->perPage = $perPage;
        $this->fields = $fields;
        $this->count = $count;
        if ($this->model instanceof Model) {
            $this->authorize('read');
        } else {
            $this->authorize('list');
        }
        return $this->execute($this->model, null);
    }

    public function count($sort, $filter, $perPage, $fields)
    {
        return $this->index($sort, $filter, $perPage, $fields, true);
    }

    protected function isBelongsTo(BelongsTo $model, Model $target = null, $data = [])
    {
        return $model->first();
    }

    protected function isBelongsToMany(BelongsToMany $model, array $targets = [], $data = [])
    {
        return $this->getPaginated($this->addSorting($this->addFilter($model)));
    }

    protected function isHasMany(HasMany $model, array $targets = [], $data = [])
    {
        return $this->getPaginated($this->addSorting($this->addFilter($model)));
    }

    protected function isHasManyThrough(HasManyThrough $model, array $targets = [], $data = [])
    {
        return $this->getPaginated($this->addSorting($this->addFilter($model)));
    }

    protected function isHasOne(HasOne $model, Model $target = null, $data = [])
    {
        return $model->first();
    }

    protected function isModel(Model $model, Model $target = null, $data = [])
    {
        return $model;
    }

    protected function isNull($model, Model $target = null, $data = [])
    {
        throw new NotFoundException($this->route);
    }

    protected function isString($model, Model $target = null, $data = [])
    {
        $result = $this->fields ? $model::select($this->fields) : $model::select();
        $query = $this->addSorting($this->addFilter($result));
        return $this->perPage != -1 || $this->count ? $this->getPaginated($query) : $query->get();
    }

    protected function isArray($model, $target = null, $data = [])
    {
        return $model;
    }

    protected function isCollection(Collection $model, $target = null, $data = [])
    {
        return $model;
    }

    /**
     *
     * &filter[]=where,username,=,"david"
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
            $params = $this->parseFilter($filter);
            $method = array_shift($params);
            $relation = false;
            if (substr($method, 0, 1) === '@') {
                $relation = substr($method, 1);
                $method = array_shift($params);
            }
            if ($relation) {
                $relFilter[$relation][] = [$method, $params];
            } else {
                $select = call_user_func_array([$select, $method], $params);
            }
        }
        foreach ($relFilter as $relationName => $relations) {
            foreach ($relations as $relation) {
                list($method, $params) = $relation;
                $select = $select->$method($relationName, function ($select) use ($params) {
                    call_user_func_array([$select, 'where'], $params);
                });
            }
        }
        return $select;
    }

    private function parseFilter($filter)
    {
        $chars = preg_split('//u', "{$filter},", null, PREG_SPLIT_NO_EMPTY);
        $current = '';
        $needsJson = false;
        $params = [];
        foreach ($chars as $ch) {
            switch ($ch) {
                case ',':
                    $value = $needsJson ? \json_decode($current) : $current;
                    if ($needsJson && $value === \null && $current !== 'null') {
                        $current .= $ch;
                        break;
                    }
                    $params[] = $value;
                    $current = '';
                    $needsJson = \false;
                    break;
                case '"':
                case '[':
                case ']':
                    $needsJson = true;
                    // no break
                default:
                    $current .= $ch;
            }
        }
        return $params;
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

    /**
     * Get a paginated result
     * If $this->perPage is less than 1 it will return all results
     *
     * @param mixed $select
     *
     * @return Collection
     */
    private function getPaginated($select)
    {
        if ($this->count) {
            return $select->count();
        } else {
            return $this->perPage > 0 ? $select->paginate($this->perPage)->getCollection() : $select->get();
        }
    }
}
