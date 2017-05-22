<?php

namespace ScayTrase\Api\Cruds\Adaptors\JmsSerializer;

use JMS\Serializer\ArrayTransformerInterface as JmsDenormalizerInterface;
use JMS\Serializer\Serializer;
use ScayTrase\Api\Cruds\EntityProcessorInterface;

final class JmsSerializerProcessor implements EntityProcessorInterface
{
    /** @var JmsDenormalizerInterface */
    private $denormalizer;

    /**
     * JmsSerializerProcessor constructor.
     *
     * @param JmsDenormalizerInterface $denormailzer
     */
    public function __construct(JmsDenormalizerInterface $denormailzer)
    {
        $this->denormalizer = $denormailzer;
    }

    /** {@inheritdoc} */
    public function updateEntity($entity, $data)
    {
        $newObject =$this->denormalizer->fromArray(
            $data,
            get_class($entity),
            JmsContextFactory::deserialization(['object_to_populate' => $entity])
        );

        return $entity;
    }
}
