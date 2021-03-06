<?php

namespace JDD\Api\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use InvalidArgumentException;
use JDD\Api\Exceptions\AuthorizationException;
use JDD\Api\Exceptions\InvalidApiCall;
use JDD\Api\Exceptions\NotFoundException;

abstract class BaseOperation
{
    protected $createNewRows = true;
    public $route;
    public $routeBase;
    public $routeModels;
    public $model;
    private $factoryStates;

    public function __construct($route, $model = null, array $factoryStates = [])
    {
        $this->route = $route;
        $this->factoryStates = $factoryStates;
        $this->model = $this->resolve($route, $model, $this->routeModels);
    }

    /**
     * Check if the loged user has access to do action on the resource
     *
     * @throws AuthorizationException
     */
    protected function authorize($action, ...$params)
    {
        if ($this->model && config('jsonapi.authorize')) {
            if (count($this->route) > 2) {
                foreach($this->routeModels as $parent) {
                    if ($parent instanceof Model) {
                        if (!Auth::user()->can('read', $parent)) {
                            throw new AuthorizationException($action, $parent, $params);
                        }
                        break;
                    }
                }
                if (!Auth::user()->can($action, [$this->model, ...$params])) {
                    throw new AuthorizationException($action, $this->model, $params);
                }
            } else {
                $model = $this->model;
                if (!Auth::user()->can($action, [$model, ...$params])) {
                    throw new AuthorizationException($action, $model, $params);
                }
            }
        }
    }

    protected function resolve($routesArray, $model = null, &$routeModels = [])
    {
        $routes = $routesArray;
        $routeModels = is_array($model) ? $model : [];
        while ($routes) {
            $route = array_shift($routes);
            if ($route === '' || !is_string($route)) {
                throw new InvalidApiCall('Invalid route component (' . json_encode($route) . ') in ' . json_encode($routesArray));
            }
            $isZero = $route == '0' || $route === 'create';
            $isNumeric = !$isZero && is_numeric($route);
            $isString = !$isZero && !$isNumeric;
            if ($model === null && $isString) {
                $model = $this->guessModel($route);
            } elseif (is_string($model) && $isZero) {
                $model = $this->createModel($model, $this->factoryStates);
            } elseif (is_string($model) && $isNumeric) {
                $model = $model::find($route);
            } elseif ($model instanceof Model && $isString) {
                $model = $model->$route();
            } elseif ($model instanceof BelongsTo && $isString) {
                $model = $model->first()->$route();
            } elseif ($model instanceof HasOne && $isString) {
                $model = $model->first()->$route();
            } elseif ($model instanceof HasMany && $isZero) {
                $model = $model->getRelated()->newInstance();
            } elseif ($model instanceof HasMany && $isNumeric) {
                $model = $model->find($route);
            } elseif ($model instanceof HasManyThrough && $isNumeric) {
                $model = $model->find($route);
            } elseif ($model instanceof BelongsToMany && $isZero) {
                $model = $model->getRelated()->newInstance();
            } elseif ($model instanceof Collection) {
                $model = $model;
            } elseif (is_array($model)) {
                $model = $model;
            } else {
                throw new NotFoundException($routesArray);
            }
            $routeModels[] = $model;
        }
        return $model;
    }

    /**
     * Create model from a $class
     *
     * @param string $class
     * @param array $states
     */
    private function createModel($class, array $states = [])
    {
        try {
            return factory($class)->states($states)->make();
        } catch (InvalidArgumentException $withoutFactory) {
            if ($states) {
                throw $withoutFactory;
            } else {
                return new $class();
            }
        }
    }

    private function getTarget($model, $data)
    {
        $target = null;
        if ($model === null) {
            //@todo could be from type of $data
        } elseif ($data === null) {
        } elseif (!empty($data['id']) && is_string($model)) {
            $target = $model::findOrFail($data['id']);
        } elseif (!empty($data['id']) && $model instanceof Model) {
            $target = $model->findOrFail($data['id']);
        } elseif (!empty($data['id'])) {
            $target = $model->getRelated()->findOrFail($data['id']);
        } elseif (!empty($data['attributes']) && is_string($model)) {
            if ($this->createNewRows) {
                $target = $model::make($data['attributes']);
            }
        } elseif (!empty($data['attributes']) && $model instanceof Model) {
            if ($this->createNewRows) {
                $target = $model->make($data['attributes']);
            }
        } elseif (!empty($data['attributes'])) {
            if ($this->createNewRows) {
                $target = $model->getRelated()->make($data['attributes']);
            }
        } elseif (array_key_exists('id', $data) && empty($data['id'])) {
            if ($this->createNewRows) {
                $target = $model::make([]);
            } else {
                $target = null;
            }
        } elseif (!isset($data['id']) && !isset($data['attributes'])) {
            $target = [];
            foreach ($data as $row) {
                $target[] = $this->getTarget($model, $row);
            }
        } else {
            throw new NotFoundException($this->route);
        }
        return $target;
    }

    protected function execute($model, $data)
    {
        $target = $this->getTarget($model, $data);
        if ($model === null) {
            $target = $this->isNull($model, $target, $data);
        } elseif (is_string($model)) {
            $target = $this->isString($model, $target, $data);
        } elseif ($model instanceof Model) {
            $target = $this->isModel($model, $target, $data);
        } elseif ($model instanceof BelongsTo) {
            $target = $this->isBelongsTo($model, $target, $data);
        } elseif ($model instanceof HasOne) {
            $target = $this->isHasOne($model, $target, $data);
        } elseif ($model instanceof HasMany) {
            $target = $this->isHasMany(
                $model,
                is_array($target) ? $target : ($target ? [$target] : []),
                $data
            );
        } elseif ($model instanceof BelongsToMany) {
            $target = $this->isBelongsToMany(
                $model,
                is_array($target) ? $target : [$target],
                $data
            );
        } elseif ($model instanceof HasManyThrough) {
            $target = $this->isHasManyThrough(
                $model,
                is_array($target) ? $target : [$target],
                $data
            );
        } elseif ($model instanceof Collection) {
            $target = $this->isCollection($model, $target, $data);
        } elseif (is_array($model)) {
            $target = $this->isArray($model, $target, $data);
        } else {
            throw new \Exception('Invalid $model ' . get_class($model));
        }
        if (isset($data['relationships'])) {
            foreach ($data['relationships'] as $rel => $json) {
                if (is_array($target)) {
                    //Case when one only model is stored to hasMany or BelongsToMany
                    $relModel = $this->resolve([$rel], $target[0]);
                } else {
                    $relModel = $this->resolve([$rel], $target);
                }
                $this->execute($relModel, $json['data']);
            }
        }
        return $target;
    }

    /**
     * Guess the model for the base name
     *
     * @param string $baseName
     *
     * @return string
     */
    private function guessModel($baseName)
    {
        $namespaces = config('jsonapi.models', ['App', 'App\Models']);
        $name = Str::studly($baseName);
        foreach ($namespaces as $namespace) {
            $guess = class_exists($class = "$namespace\\$name") ? $class
            : (class_exists($class = "$namespace\\" . Str::singular($name)) ? $class
            : (class_exists($class = "$namespace\\" . Str::plural($name)) ? $class : null));
            if ($guess) {
                return $guess;
            }
        }
    }

    abstract protected function isBelongsTo(
        BelongsTo $model,
        Model $target = null,
        $data = []
    );

    abstract protected function isBelongsToMany(
        BelongsToMany $model,
        array $targets = [],
        $data = []
    );

    abstract protected function isHasMany(HasMany $model, array $targets = [], $data = []);

    abstract protected function isHasOne(HasOne $model, Model $target = null, $data = []);

    abstract protected function isModel(Model $model, Model $target = null, $data = []);

    abstract protected function isNull($model, Model $target = null, $data = []);

    abstract protected function isString($model, Model $target = null, $data = []);

    abstract protected function isHasManyThrough(HasManyThrough $model, array $targets = [], $data = []);
}
