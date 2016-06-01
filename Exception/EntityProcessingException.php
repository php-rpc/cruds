<?php

namespace ScayTrase\Api\Cruds\Exception;

class EntityProcessingException extends \RuntimeException implements CrudsExceptionInterface
{
    /**
     * @param string $message
     * @param mixed  $data
     *
     * @return static
     */
    public static function invalidDataSubmitted($message, $data)
    {
        return new static(
            sprintf('Data submitted to processor is invalid: %s', $message)
        );
    }
}
