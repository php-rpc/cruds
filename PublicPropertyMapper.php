<?php

namespace ScayTrase\Api\Cruds;

final class PublicPropertyMapper implements PropertyMapperInterface
{
    /** @var \ReflectionClass[] */
    private $reflections = [];

    /** {@inheritdoc} */
    public function getEntityProperty($className, $apiProperty)
    {
        if (in_array($apiProperty, $this->getApiProperties($className), true)) {
            return $apiProperty;
        }

        return null;
    }

    /** {@inheritdoc} */
    public function getApiProperty($className, $objectProperty)
    {
        if (in_array($objectProperty, $this->getEntityProperties($className), true)) {
            return $objectProperty;
        }

        return null;
    }

    /** {@inheritdoc} */
    public function getApiProperties($className)
    {
        return $this->getEntityProperties($className);
    }

    /** {@inheritdoc} */
    public function getEntityProperties($className)
    {
        return array_map(
            [$this, 'getPropertyName'],
            $this->getReflection($className)->getProperties(\ReflectionProperty::IS_PUBLIC)
        );
    }

    /**
     * @param $className
     *
     * @return \ReflectionClass
     */
    private function getReflection($className)
    {
        if (!array_key_exists($className, $this->reflections)) {
            $this->reflections[$className] = new \ReflectionClass($className);
        }

        return $this->reflections[$className];
    }

    private function getPropertyName(\ReflectionProperty $property)
    {
        return $property->name;
    }
}
