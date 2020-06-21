<?php

namespace JDD\Api\Exceptions;

use Exception;

/**
 * Description of InvalidApiCall
 *
 * @author davidcallizaya
 */
class InvalidApiCall extends Exception
{
    /**
     * Invalid API call.
     *
     */
    public function __construct()
    {
        parent::__construct(__('jdd-api::exceptions.InvalidApiCall', compact('model')));
    }
}
