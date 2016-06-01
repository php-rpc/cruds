<?php

namespace ScayTrase\Api\Cruds;

interface ObjectFactoryInterface
{
    /**
     * Creates object from data
     *
     * @param mixed $data
     *
     * @return object
     */
    public function createObject($data);
}
