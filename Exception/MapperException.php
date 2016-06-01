<?php

namespace ScayTrase\Api\Cruds\Exception;

class MapperException extends \RuntimeException implements CrudsExceptionInterface
{
    public static function unsupportedClass($class)
    {
        return new static(
            sprintf('Class "%s" is not supported by this mapper', $class)
        );
    }
}
