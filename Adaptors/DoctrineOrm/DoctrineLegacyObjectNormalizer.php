<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * Symfony 3.1 ported class
 */
final class DoctrineLegacyObjectNormalizer extends AbstractNormalizer
{
    /** @var ManagerRegistry */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function setRegistry(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && null !== $this->registry->getManagerForClass(get_class($data));
    }

    /** {@inheritdoc} */
    public function normalize($object, $format = null, array $context = [])
    {
        if ($this->isCircularReference($object, $context)) {
            return $this->handleCircularReference($object);
        }

        $data             = [];
        $attributes       = $this->extractAttributes($object, $context);
        $accessor         = new AssociationNormalizer($this->registry);
        $propertyAccessor = new PropertyAccessor();

        foreach ($attributes as $attribute) {
            $attributeValue   = $propertyAccessor->getValue($object, $attribute);

            $renderAttribute = $attribute;
            if ($this->nameConverter) {
                $renderAttribute = $this->nameConverter->normalize($attribute);
            }

            $data[$renderAttribute] = $accessor->normalize($attributeValue, $object, $attribute);
        }

        return $data;
    }


    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $allowedAttributes = $this->getAllowedAttributes($class, $context, true);
        $normalizedData    = $this->prepareForDenormalization($data);

        $reflectionClass = new \ReflectionClass($class);
        $object          =
            $this->instantiateObject($normalizedData, $class, $context, $reflectionClass, $allowedAttributes);

        $accessor         = new AssociationNormalizer($this->registry);
        $propertyAccessor = new PropertyAccessor();

        foreach ($normalizedData as $attribute => $value) {
            if ($this->nameConverter) {
                $attribute = $this->nameConverter->denormalize($attribute);
            }

            $allowed = $allowedAttributes === false || in_array($attribute, $allowedAttributes, true);
            $ignored = in_array($attribute, $this->ignoredAttributes, true);

            if ($allowed && !$ignored) {
                try {
                    $value = $accessor->denormalize($object, $attribute, $value);
                    $propertyAccessor->setValue($object, $attribute, $value);
                } catch (NoSuchPropertyException $exception) {
                    // Properties not found are ignored
                }
            }
        }

        return $object;
    }

    /** {@inheritdoc} */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return null !== $this->registry->getManagerForClass(get_class($data));
    }

    /**
     * @param       $object
     * @param array $context
     *
     * @return array
     */
    private function extractAttributes($object, array $context)
    {
        $attributes = $this->getAllowedAttributes($object, $context, true);

        // If not using groups, detect manually
        if (false === $attributes) {
            $attributes = [];

            // methods
            $reflClass = new \ReflectionClass($object);
            foreach ($reflClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod) {
                if (
                    !$reflMethod->isStatic() &&
                    !$reflMethod->isConstructor() &&
                    !$reflMethod->isDestructor() &&
                    0 === $reflMethod->getNumberOfRequiredParameters()
                ) {
                    $name = $reflMethod->getName();

                    if (strpos($name, 'get') === 0 || strpos($name, 'has') === 0) {
                        // getters and hassers
                        $attributes[lcfirst(substr($name, 3))] = true;
                    } elseif (strpos($name, 'is') === 0) {
                        // issers
                        $attributes[lcfirst(substr($name, 2))] = true;
                    }
                }
            }

            // properties
            foreach ($reflClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflProperty) {
                if (!$reflProperty->isStatic()) {
                    $attributes[$reflProperty->getName()] = true;
                }
            }

            $attributes = array_keys($attributes);

            return $attributes;
        }

        return $attributes;
    }
}
