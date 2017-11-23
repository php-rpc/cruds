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
     * SymfonyPropertyMapper constructor.
     *
     * @param ClassMetadataFactoryInterface $factory
     * @param NameConverterInterface $strategy
     */
    public function __construct(ClassMetadataFactoryInterface $factory, NameConverterInterface $strategy)
    {
        $this->factory = $factory;
        $this->strategy = $strategy;
    }

    /** {@inheritdoc} */
    public function getEntityProperty($className, $apiProperty)
    {
        if (!$this->factory->hasMetadataFor($className)) {
            return null;
        }

        if (!in_array($apiProperty, $this->getApiProperties($className), true)) {
            return null;
        }

        return $this->strategy->denormalize($apiProperty);
    }

    /** {@inheritdoc} */
    public function getApiProperty($className, $objectProperty)
    {
        if (!$this->factory->hasMetadataFor($className)) {
            return null;
        }

        if (!in_array($objectProperty, $this->getEntityProperties($className), true)) {
            return null;
        }

        return $this->strategy->normalize($objectProperty);
    }

    /** {@inheritdoc} */
    public function getApiProperties($className): array
    {
        $properties = [];
        foreach ($this->getMetadata($className) as $property) {
            $properties[] = $this->strategy->denormalize($property->getName());
        }

        return $properties;
    }

    /** {@inheritdoc} */
    public function getEntityProperties($className): array
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
    private function getMetadata($className): array
    {
        try {
            return $this->factory->getMetadataFor($className)->getAttributesMetadata();
        } catch (\InvalidArgumentException $exception) {
            throw MapperException::unsupportedClass($className);
        }
    }
}
