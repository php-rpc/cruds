<?php

namespace ScayTrase\Api\Cruds\Adaptors\JmsSerializer;

use JMS\Serializer\Metadata\PropertyMetadata;
use Metadata\MetadataFactoryInterface;
use ScayTrase\Api\Cruds\Exception\MapperException;
use ScayTrase\Api\Cruds\PropertyMapperInterface;

final class JmsPropertyMapper implements PropertyMapperInterface
{
    /** @var MetadataFactoryInterface */
    private $factory;

    /**
     * JmsPropertyMapper constructor.
     *
     * @param MetadataFactoryInterface $factory
     */
    public function __construct(MetadataFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /** {@inheritdoc} */
    public function getObjectProperty($className, $apiProperty)
    {
        $metadata = $this->getMetadata($className);

        foreach ($metadata->propertyMetadata as $propertyMetadata) {
            /** @var PropertyMetadata $propertyMetadata */
            if ($propertyMetadata->serializedName === $apiProperty) {
                return $propertyMetadata->reflection ? $propertyMetadata->reflection->getName() : null;
            }
        }

        return null;
    }

    /** {@inheritdoc} */
    public function getApiProperty($className, $objectProperty)
    {
        $metadata = $this->getMetadata($className);

        if (!array_key_exists($objectProperty, $metadata->propertyMetadata)) {
            return null;
        }

        return $metadata->propertyMetadata[$objectProperty]->serializedName;
    }

    /** {@inheritdoc} */
    public function getApiProperties($className)
    {
        $metadata = $this->getMetadata($className);

        $apiProperties = [];

        foreach ($metadata->propertyMetadata as $propertyMetadata) {
            /** @var PropertyMetadata $propertyMetadata */
            $apiProperties[] = $propertyMetadata->serializedName;
        }

        return $apiProperties;
    }

    /** {@inheritdoc} */
    public function getObjectProperties($className)
    {
        $metadata = $this->getMetadata($className);

        $objectProperties = [];

        foreach ($metadata->propertyMetadata as $propertyMetadata) {
            /** @var PropertyMetadata $propertyMetadata */
            if (null !== $propertyMetadata->reflection) {
                $objectProperties[] = $propertyMetadata->reflection->getName();
            }
        }

        return $objectProperties;
    }

    /**
     * @param $className
     *
     * @return \Metadata\ClassHierarchyMetadata|\Metadata\MergeableClassMetadata|null
     * @throws MapperException
     */
    private function getMetadata($className)
    {
        $metadata = $this->factory->getMetadataForClass($className);

        if (null === $metadata) {
            throw MapperException::unsupportedClass($className);
        }

        return $metadata;
    }
}
