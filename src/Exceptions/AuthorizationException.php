<?php

namespace JDD\Api\Exceptions;

use Exception;

/**
 * AuthorizationException
 *
 */
class AuthorizationException extends Exception
{
    /**
     * User is not athorized.
     */
    public function __construct($action, $model, array $params)
    {
        $model = str_replace('\\', '.', is_object($model) ? get_class($model) : $model);
        if ($action === 'callMethod') {
            $action = $params[0];
        }
        if ($action === 'callStaticMethod') {
            $action = '&' . $params[0];
        }
        parent::__construct(__('jdd-api::exceptions.AuthorizationException', compact('action', 'model')));
    }
}
