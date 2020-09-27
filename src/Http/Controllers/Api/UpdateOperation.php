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

class UpdateOperation extends BaseOperation
{
    protected $createNewRows = false;

    public function update($data)
    {
        return $this->execute($this->model, $data);
    }

    protected function isBelongsTo(BelongsTo $model, Model $target = null, $data = [])
    {
        if ($target === null) {
            $model->dissociate();
        } else {
            $this->updateModel($target, $data);
            $model->associate($target);
        }
        $model->getParent()->save();
        return $target;
    }

    protected function isBelongsToMany(
        BelongsToMany $model,
        array $targets = [],
        $data = []
    ) {
        $ids = [];
        foreach ($targets as $target) {
            $this->updateModel($target, $data);
            $ids[] = $target->id;
        }
        $model->sync($ids);
        return $targets;
    }

    protected function isHasMany(HasMany $model, array $targets = [], $data = [])
    {
        foreach ($targets as $target) {
            $this->updateModel($target, $data);
        }
        $model->saveMany($targets);
        return $targets;
    }

    protected function isHasManyThrough(HasManyThrough $model, array $targets = [], $data = [])
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
            $this->updateModel($target, $data);
            $menuRole = $model->getParent();
            $menuRole->$menuRoleMenuIdR = $target->$menuIdK;
            $menuRole->$menuRoleRoleR = $owner->$ownerKey;
            $menuRole->save();
        }
        return $targets;
    }

    protected function isHasOne(HasOne $model, Model $target = null, $data = [])
    {
        $this->updateModel($target, $data);
        $model->save($target);
        return $target;
    }

    protected function isModel(Model $model, Model $target = null, $data = [])
    {
        $this->updateModel($model, $data);
        return $model;
    }

    protected function isNull($model, Model $target = null, $data = [])
    {
        throw new NotFoundException($this->route);
    }

    protected function isString($model, Model $target = null, $data = [])
    {
        throw new InvalidApiCall;
    }

    private function updateModel(Model $target, $data)
    {
        if (isset($data['attributes'])) {
            $target->update($data['attributes']);
        }
        return $target;
    }
}
