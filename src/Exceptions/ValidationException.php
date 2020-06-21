<?php

namespace JDD\Api\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException as Base;

/**
 * ValidationException with translation
 *
 */
class ValidationException extends Base
{

    /**
     * Create a new exception instance.
     *
     * @param  \Illuminate\Validation\ValidationException $exception
     *
     * @return void
     */
    public function __construct(Base $exception)
    {
        Exception::__construct(__('jdd-api::exceptions.ValidationException', []));

        $this->response = $exception->response;
        $this->errorBag = $exception->errorBag;
        $this->validator = $exception->validator;
    }
}
