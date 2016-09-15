<?php

namespace ScayTrase\Api\Cruds\Exception;

class CriteriaConfigurationException extends \InvalidArgumentException implements CrudsExceptionInterface
{
    public static function unknown(array $names)
    {
        return new static(sprintf('Unknown criteria: %s', implode(', ', $names)));
    }

    public static function invalidType($expected, $actual)
    {
        return new static(sprintf('Invalid criteria: %s expected, %s given', $expected, $actual));
    }

    public static function invalidData($field)
    {
        return new static(sprintf('Invalid criteria: %s', $field));
    }

    public static function invalidPropertyType($property, $expected, $actual)
    {
        return new static(
            sprintf('Invalid criteria property "%s": %s expected, %s given', $property, $expected, $actual)
        );
    }

    public static function invalidProperty($property, \Exception $exception)
    {
        return new static(sprintf('Invalid criteria property "%s": %s', $property, $exception->getMessage()));
    }
}
