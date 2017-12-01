<?php

namespace ScayTrase\Api\Cruds;

use ScayTrase\Api\Cruds\Exception\EntityProcessingException;

interface EntityProcessorInterface
{
    /**
     * @param object $entity
     * @param mixed  $data
     *
     * @return object
     * @throws EntityProcessingException
     */
    public function updateEntity($entity, $data);
}
