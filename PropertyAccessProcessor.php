<?php

namespace ScayTrase\Api\Cruds;

use ScayTrase\Api\Cruds\Exception\EntityProcessingException;
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

final class PropertyAccessProcessor implements EntityProcessorInterface
{
    /** @var  PropertyAccessor */
    private $accessor;

    /**
     * PropertyAccessProcessor constructor.
     */
    public function __construct()
    {
        $this->accessor = new PropertyAccessor();
    }

    /** {@inheritdoc} */
    public function updateEntity($entity, $data)
    {
        try {
            foreach ((array)$data as $property => $value) {
                $this->accessor->setValue($entity, $property, $value);
            }
        } catch (ExceptionInterface $exception) {
            throw new EntityProcessingException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $entity;
    }
}
