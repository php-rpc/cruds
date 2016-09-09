<?php

namespace ScayTrase\Api\Cruds\Adaptors\Symfony;

use ScayTrase\Api\Cruds\Exception\MapperException;
use ScayTrase\Api\Cruds\PropertyMapperInterface;
use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class SymfonyPropertyMapper implements PropertyMapperInterface
{
    /** @var  ClassMetadataFactoryInterface */
    private $factory;
    /** @var  NameConverterInterface */
    private $strategy;

    /**
     * @param string $className
     * @param string $apiProperty
     *
     * @return null|string
     * @throws MapperException
     */
    public function getObjectProperty($className, $apiProperty)
    {
        if ($this->factory->hasMetadataFor($className)) {
            return null;
        }

        if (!in_array($apiProperty, $this->getApiProperties($className), true)) {
            return null;
        }

        return $this->strategy->denormalize($apiProperty);
    }

    /**
     * @param string $className
     * @param string $objectProperty
     *
     * @return null|string
     * @throws MapperException
     */
    public function getApiProperty($className, $objectProperty)
    {
        if ($this->factory->hasMetadataFor($className)) {
            return null;
        }

        if (!in_array($objectProperty, $this->getObjectProperties($className), true)) {
            return null;
        }

        return $this->strategy->normalize($objectProperty);
    }

    /**
     * @param string $className
     *
     * @return string[]
     * @throws MapperException
     */
    public function getApiProperties($className)
    {
        $properties = [];
        foreach ($this->getMetadata($className) as $property) {
            $properties[] = $this->strategy->denormalize($property->getName());
        }

        return $properties;
    }

    /**
     * @param string $className
     *
     * @return string[]
     * @throws MapperException
     */
    public function getObjectProperties($className)
    {
        $properties = [];
        foreach ($this->getMetadata($className) as $property) {
            $properties[] = $property->getName();
        }

        return $properties;
    }

    /**
     * @param $className
     *
     * @return AttributeMetadataInterface[]
     */
    private function getMetadata($className)
    {
        return $this->factory->getMetadataFor($className)->getAttributesMetadata();
    }
}
