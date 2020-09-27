<?php

namespace JDD\Api\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use JDD\Api\Exceptions\NotFoundException;
use ReflectionMethod;

class CallOperation extends BaseOperation
{
    protected $createNewRows = false;

    public function callMethod($call)
    {
        $this->call = $call;
        return $this->execute($this->model, null);
    }

    protected function isBelongsTo(BelongsTo $model, Model $target = null, $data = [])
    {
        return $this->doModelCall($model->getRelated());
    }

    protected function isBelongsToMany(
        BelongsToMany $model,
        array $targets = [],
        $data = []
    ) {
        return $this->doModelCall($model->getRelated());
    }

    protected function isHasMany(HasMany $model, array $targets = [], $data = [])
    {
        return $this->doModelCall($model->getRelated());
    }

    protected function isHasManyThrough(HasManyThrough $model, array $targets = [], $data = [])
    {
        return $this->doModelCall($model->getRelated());
    }

    protected function isHasOne(HasOne $model, Model $target = null, $data = [])
    {
        return $this->doModelCall($model->getRelated());
    }

    protected function isModel(Model $model, Model $target = null, $data = [])
    {
        return $this->doModelCall($model);
    }

    protected function isNull($model, Model $target = null, $data = [])
    {
        throw new NotFoundException($this->route);
    }

    protected function isString($model, Model $target = null, $data = [])
    {
        $modelC = new $model();
        return $this->doModelCall($modelC);
    }

    private function doModelCall(Model $model)
    {
        $method = $this->call['method'];
        $reflection = new ReflectionMethod($model, $method);
        $args = [];
        foreach ($reflection->getParameters() as $param) {
            $args[] = isset($this->call['parameters'][$param->getName()]) ?
                $this->call['parameters'][$param->getName()] :
                ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);
        }
        if (is_string($model)) {
            $model = new $model();
        }
        return $model->$method(...$args);
    }
}
