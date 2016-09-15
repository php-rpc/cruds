<?php

namespace ScayTrase\Api\Cruds\Adaptors\Symfony;

use ScayTrase\Api\Cruds\EntityProcessorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class SerializerProcessor implements EntityProcessorInterface
{
    /** @var  DenormalizerInterface */
    private $denormalizer;

    /**
     * SerializerProcessor constructor.
     *
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    /** {@inheritdoc} */
    public function updateEntity($entity, $data)
    {
        return $this->denormalizer->denormalize($this, get_class($entity), ['object_to_populate' => $entity]);
    }
}
