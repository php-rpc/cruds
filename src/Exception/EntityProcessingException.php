<?php

namespace ScayTrase\Api\Cruds\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Yaml\Yaml;

class EntityProcessingException extends \RuntimeException implements CrudsExceptionInterface
{
    /**
     * @param string $message
     *
     * @return static
     */
    public static function invalidDataSubmitted($message)
    {
        return new static(
            sprintf('Data submitted to processor is invalid: %s', $message)
        );
    }

    public static function fromViolationList(ConstraintViolationListInterface $list)
    {
        $errors = [];
        foreach ($list as $violation) {
            /** @var ConstraintViolationInterface $violation */
            $errors[$violation->getPropertyPath()] = [
                'message'       => $violation->getMessage(),
                'code'          => $violation->getCode(),
                'root'          => $violation->getRoot(),
                'invalid_value' => $violation->getInvalidValue(),
            ];
        }

        return new static(
            'Invalid data submitted for entity: ' . PHP_EOL .
            Yaml::dump($errors)
        );
    }

}
