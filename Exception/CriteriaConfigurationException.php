<?php

namespace ScayTrase\Api\Cruds\Exception;

class CriteriaConfigurationException extends \InvalidArgumentException implements CrudsExceptionInterface
{
    public static function unknown(array $names)
    {
        return new self(sprintf('Unknown criteria configurators: %s', implode(', ', $names)));
    }

    public static function invalid($name, $value)
    {
        return new self(sprintf('Invalid data passed to criteria configurators "%s": %s', $name, json_encode($value)));
    }

    public static function invalidType($expected, $actual)
    {
        return new self(
            sprintf('Invalid data format passed to criteria configurator: %s expected, %s given', $expected, $actual)
        );
    }
}
