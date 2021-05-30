<?php

namespace JDD\Api\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use JDD\Api\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use JDD\Api\Exceptions\InvalidApiCall;

class DeleteOperation extends BaseOperation
{
    protected $createNewRows = false;

    public function delete()
    {
        $this->authorize('delete');
        return $this->execute($this->model, null);
    }

    protected function isBelongsTo(BelongsTo $model, Model $target = null, $data = [])
    {
        $model->dissociate();
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
            $ids[] = $target->id;
        }
        $model->detach($ids);
        return $targets;
    }

    protected function isHasMany(HasMany $model, array $targets = [], $data = [])
    {
        if ($targets) {
            foreach ($targets as $target) {
                $target->delete();
            }
        } else {
            $model->delete();
        }
        return $targets;
    }

    protected function isHasManyThrough(HasManyThrough $model, array $targets = [], $data = [])
    {
        // Get Owner: Far Parent
        $reflection = new ReflectionClass($model);
        $property = $reflection->getProperty('farParent');
        $property->setAccessible(true);
        // Save targets and add relationship
        foreach($targets as $target) {
            $target->delete();
            $menuRole = $model->getParent();
            $menuRole->delete();
        }
        return $targets;
    }

    protected function isHasOne(HasOne $model, Model $target = null, $data = [])
    {
        //@todo: Does Eloquent implements detach on HasOn relationship?
        //$model->detach($target);
        $target->setAttribute($model->getForeignKeyName(), null);
        $target->save();
        return $target;
    }

    protected function isModel(Model $model, Model $target = null, $data = [])
    {
        $model->delete();
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
}
