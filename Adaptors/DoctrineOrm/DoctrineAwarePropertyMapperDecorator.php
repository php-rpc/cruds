<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Persistence\ManagerRegistry;
use ScayTrase\Api\Cruds\Exception\MapperException;
use ScayTrase\Api\Cruds\PropertyMapperInterface;

final class DoctrineAwarePropertyMapperDecorator implements PropertyMapperInterface
{
    /** @var  PropertyMapperInterface */
    private $decorated;
    /** @var  ManagerRegistry */
    private $registry;

    /**
     * DoctrineAwarePropertyMapperDecorator constructor.
     *
     * @param PropertyMapperInterface $decorated
     * @param ManagerRegistry         $registry
     */
    public function __construct(PropertyMapperInterface $decorated, ManagerRegistry $registry)
    {
        $this->decorated = $decorated;
        $this->registry  = $registry;
    }

    /** {@inheritdoc} */
    public function getObjectProperty($className, $apiProperty)
    {
        return $this->decorated->getObjectProperty($this->normalizeClassName($className), $apiProperty);
    }

    /** {@inheritdoc} */
    public function getApiProperty($className, $objectProperty)
    {
        return $this->decorated->getApiProperty($this->normalizeClassName($className), $objectProperty);
    }

    /** {@inheritdoc} */
    public function getApiProperties($className)
    {
        return $this->decorated->getApiProperties($this->normalizeClassName($className));
    }

    /** {@inheritdoc} */
    public function getObjectProperties($className)
    {
        return $this->decorated->getObjectProperties($this->normalizeClassName($className));
    }

    private function normalizeClassName($className)
    {
        $manager = $this->registry->getManagerForClass($className);

        if (null === $manager) {
            throw MapperException::unsupportedClass($className);
        }

        $metadata = $manager->getClassMetadata($className);

        return $metadata->getName();
    }
}
