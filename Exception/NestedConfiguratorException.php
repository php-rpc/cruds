<?php

namespace ScayTrase\Api\Cruds\Exception;

final class NestedConfiguratorException extends CriteriaConfigurationException
{
    public static function invalidNesting($filter, \Exception $exception)
    {
        return new static(
            sprintf('Invalid criteria "%s": %s', $filter, $exception->getMessage()),
            $exception->getCode(),
            $exception
        );
    }

    public static function unknown(array $names)
    {
        return new static(sprintf('Unknown criteria: %s', implode(', ', $names)));
    }
}
