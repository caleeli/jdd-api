<?php

namespace JDD\Api\Traits;

/**
 * AjaxFilterTrait
 *
 * filter[]=whereAjaxFilter,"search",attributes.name,attributes.description,relationships.role.name
 */
trait AjaxFilterTrait
{
    public function scopeWhereAjaxFilter($query, $filter, ...$fields)
    {
        if (empty($filter) || empty($fields)) {
            return $query;
        }
        return $query->where(function ($select) use ($filter, $fields) {
            $relFilter = [];
            foreach ($fields as $field) {
                $relation = false;
                if (substr($field, 0, 14) === 'relationships.') {
                    list($relation, $subField) = explode('.', substr($field, 14));
                    $params = [$subField, 'like', "%$filter%"];
                    $relFilter[$relation][] = ['orWhereHas', $params];
                } elseif (substr($field, 0, 11) === 'attributes.') {
                    $params = [substr($field, 11), 'like', "%$filter%"];
                    $select = $select->orWhere(...$params);
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
        });
    }
}
