<?php

namespace JDD\Api\Exceptions;

use Exception;
use Illuminate\Support\Str;

/**
 * Description of NotFoundException
 *
 * @author davidcallizaya
 */
class NotFoundException extends Exception
{

    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct($route)
    {
        $model = implode('.', array_map(function ($item) {
            return ucfirst(Str::camel($item));
        }, $route));
        parent::__construct(__('jdd-api::exceptions.NotFoundException', compact('model')));
    }
}
