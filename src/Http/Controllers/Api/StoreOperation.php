<?php

namespace JDD\Api\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use JDD\Api\Exceptions\InvalidApiCall;
use JDD\Api\Exceptions\NotFoundException;
use ReflectionClass;

class StoreOperation extends BaseOperation
{
    public function store($data)
    {
        return $this->execute($this->model, $data);
    }

    protected function isBelongsTo(BelongsTo $model, Model $target = null, $data = [])
    {
        $model->associate($target);
        $model->getParent()->save();
        return $target;
    }

    protected function isBelongsToMany(BelongsToMany $model, array $targets, $data)
    {
        $ids = [];
        foreach ($targets as $target) {
            $ids[] = $target->id;
        }
        $model->sync($ids);
        return $targets;
    }

    protected function isHasMany(HasMany $model, array $targets, $data)
    {
        $model->saveMany($targets);
        return $targets;
    }

    protected function isHasManyThrough(HasManyThrough $model, array $targets, $data)
    {
        // Get Owner: Far Parent
        $reflection = new ReflectionClass($model);
        $property = $reflection->getProperty('farParent');
        $property->setAccessible(true);
        $owner = $property->getValue($model);
        // Get keys
        $menuIdK = $model->getForeignKeyName();
        $menuRoleMenuIdR = $model->getSecondLocalKeyName();
        $menuRoleRoleR = $model->getFirstKeyName();
        $ownerKey = $model->getLocalKeyName();
        // Save targets and add relationship
        foreach ($targets as $target) {
            $target->save();
            $menuRole = $model->getParent();
            $menuRole->$menuRoleMenuIdR = $target->$menuIdK;
            $menuRole->$menuRoleRoleR = $owner->$ownerKey;
            $menuRole->save();
        }
        return $targets;
    }

    protected function isHasOne(HasOne $model, Model $target, $data)
    {
        $model->save($target);
        return $target;
    }

    protected function isModel(Model $model, Model $target, $data)
    {
        throw new InvalidApiCall();
    }

    protected function isNull($model, Model $target, $data)
    {
        throw new NotFoundException();
    }

    protected function isString($model, Model $target, $data)
    {
        $target->save();
        return $target;
    }
}
