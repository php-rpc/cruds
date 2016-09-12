<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class AssociationPropertyAccessor implements PropertyAccessorInterface
{
    /** @var  PropertyAccessorInterface */
    private $delegate;
    /** @var  ManagerRegistry */
    private $registry;
    /** @var  AssociationNormalizer */
    private $normalizer;

    /**
     * AssociationPropertyAccessor constructor.
     *
     * @param PropertyAccessorInterface $delegate
     * @param ManagerRegistry           $registry
     */
    public function __construct(PropertyAccessorInterface $delegate, ManagerRegistry $registry)
    {
        $this->delegate = $delegate;
        $this->registry = $registry;

        $this->normalizer = new AssociationNormalizer($this->registry);
    }

    /** {@inheritdoc} */
    public function setValue(&$objectOrArray, $propertyPath, $value)
    {
        $value = $this->normalizer->denormalize($objectOrArray, $propertyPath, $value);

        return $this->delegate->setValue($objectOrArray, $propertyPath, $value);
    }

    /** {@inheritdoc} */
    public function getValue($objectOrArray, $propertyPath)
    {
        return $this->normalizer->normalize(
            $this->delegate->getValue($objectOrArray, $propertyPath),
            $objectOrArray,
            $propertyPath
        );
    }

    /** {@inheritdoc} */
    public function isWritable($objectOrArray, $propertyPath)
    {
        return $this->delegate->isWritable($objectOrArray, $propertyPath);
    }

    /** {@inheritdoc} */
    public function isReadable($objectOrArray, $propertyPath)
    {
        return $this->delegate->isReadable($objectOrArray, $propertyPath);
    }
}
