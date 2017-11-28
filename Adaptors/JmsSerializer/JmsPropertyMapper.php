<?php

namespace ScayTrase\Api\Cruds\Adaptors\JmsSerializer;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Metadata\MetadataFactoryInterface;
use ScayTrase\Api\Cruds\Exception\MapperException;
use ScayTrase\Api\Cruds\PropertyMapperInterface;

final class JmsPropertyMapper implements PropertyMapperInterface
{
    /** @var MetadataFactoryInterface */
    private $factory;
    /** @var  PropertyNamingStrategyInterface */
    private $strategy;

    /**
     * JmsPropertyMapper constructor.
     *
     * @param MetadataFactoryInterface        $factory
     * @param PropertyNamingStrategyInterface $strategy
     */
    public function __construct(MetadataFactoryInterface $factory, PropertyNamingStrategyInterface $strategy)
    {
        $this->factory  = $factory;
        $this->strategy = $strategy;
    }

    /** {@inheritdoc} */
    public function getEntityProperty($className, $apiProperty)
    {
        $metadata = $this->getMetadata($className);

        foreach ($metadata->propertyMetadata as $propertyMetadata) {
            /** @var PropertyMetadata $propertyMetadata */
            if ($this->getPropertyName($propertyMetadata) === $apiProperty) {
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

        return $this->getPropertyName($metadata->propertyMetadata[$objectProperty]);
    }

    /** {@inheritdoc} */
    public function getApiProperties($className)
    {
        $metadata = $this->getMetadata($className);

        $apiProperties = [];

        foreach ($metadata->propertyMetadata as $propertyMetadata) {
            /** @var PropertyMetadata $propertyMetadata */
            $apiProperties[] = $this->getPropertyName($propertyMetadata);
        }

        return $apiProperties;
    }

    /** {@inheritdoc} */
    public function getEntityProperties($className)
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

    /**
     * @param PropertyMetadata $propertyMetadata
     *
     * @return string
     */
    private function getPropertyName(PropertyMetadata $propertyMetadata)
    {
        return $this->strategy->translateName($propertyMetadata);
    }
}
