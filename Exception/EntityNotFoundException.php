<?php

namespace ScayTrase\Api\Cruds\Exception;

class EntityNotFoundException extends \InvalidArgumentException implements CrudsExceptionInterface
{
    public static function byIdentifier($identifier)
    {
        return new static(sprintf('Entity not found by given identifier: %s', json_encode($identifier)));
    }
}
