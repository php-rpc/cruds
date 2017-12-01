<?php

namespace ScayTrase\Api\Cruds;

interface EntityFactoryInterface
{
    /**
     * Creates object from data
     *
     * @param mixed $data
     *
     * @return object
     */
    public function createEntity($data);
}
