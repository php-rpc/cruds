<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

final class AssociationNormalizer
{
    /** @var  ManagerRegistry */
    private $registry;

    /**
     * AssociationAccessor constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function normalize($rawValue, $object, $attribute)
    {
        $normalizer = new EntityToIdNormalizer($this->registry);

        if (null === $rawValue) {
            return null;
        }

        $metadata = $this->getObjectMetadata($object, $attribute);
        if (null === $metadata) {
            return $rawValue;
        }

        if ($metadata->isSingleValuedAssociation($attribute)) {
            return $normalizer->normalize($rawValue);
        }

        if ($rawValue instanceof \Traversable) {
            $rawValue = iterator_to_array($rawValue);
        }

        return array_map([$normalizer, 'normalize'], $rawValue);
    }

    public function denormalize($object, $attribute, $value)
    {
        if (null === $value) {
            return null;
        }

        $normalizer = new EntityToIdNormalizer($this->registry);
        $metadata   = $this->getObjectMetadata($object, $attribute);

        if (null === $metadata) {
            return $value;
        }

        if ($metadata->isSingleValuedAssociation($attribute)) {
            return $normalizer->denormalize($value, $metadata->getAssociationTargetClass($attribute));
        }

        $objects = new ArrayCollection();
        foreach ($value as $item) {
            $objects->add($normalizer->denormalize($item, $metadata->getAssociationTargetClass($attribute)));
        }

        return $objects;
    }

    /**]
     * @param $object
     * @param $attribute
     *
     * @return ClassMetadata|null
     */
    private function getObjectMetadata($object, $attribute)
    {
        $class = get_class($object);

        $manager = $this->registry->getManagerForClass($class);
        if (null === $manager) {
            return null;
        }

        $metadata = $manager->getClassMetadata($class);
        if (!$metadata->hasAssociation($attribute)) {
            return null;
        }

        return $metadata;
    }
}
