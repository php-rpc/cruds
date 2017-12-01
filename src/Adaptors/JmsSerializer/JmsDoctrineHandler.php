<?php

namespace ScayTrase\Api\Cruds\Adaptors\JmsSerializer;

use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\VisitorInterface;
use ScayTrase\Api\Cruds\Adaptors\DoctrineOrm\EntityToIdNormalizer;

final class JmsDoctrineHandler
{
    const TYPE = 'DoctrineAssociation';

    /** @var  EntityToIdNormalizer */
    private $converter;
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * JmsDoctrineHandler constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry  = $registry;
        $this->converter = new EntityToIdNormalizer($this->registry);
    }

    public function serializeRelation(VisitorInterface $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }

        if (is_array($relation)) {
            return array_map([$this->converter, 'normalize'], $relation);
        }

        return $this->converter->normalize($relation);
    }

    public function deserializeRelation(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $metadatas = iterator_to_array($context->getMetadataStack());
        /** @var PropertyMetadata $property */
        $property = array_shift($metadatas);
        /** @var ClassMetadata $class */
        $class = array_shift($metadatas);

        $metadata = $this->registry->getManagerForClass($class->name)->getClassMetadata($class->name);

        return $this->converter->denormalize($data, $metadata->getAssociationTargetClass($property->name));
    }
}
