<?php

namespace JDD\Api\Models;

class ArrayWrapper
{
    /**
     * @var array $data
     */
    private $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function paginate()
    {
        return $this;
    }

    public function getCollection()
    {
        return $this->data;
    }
}
