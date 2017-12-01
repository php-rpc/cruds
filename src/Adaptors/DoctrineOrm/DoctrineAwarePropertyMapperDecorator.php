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
    public function getEntityProperty($className, $apiProperty)
    {
        return $this->decorated->getEntityProperty($this->normalizeClassName($className), $apiProperty);
    }

    /** {@inheritdoc} */
    public function getApiProperty($className, $objectProperty)
    {
        return $this->decorated->getApiProperty($this->normalizeClassName($className), $objectProperty);
    }

    /** {@inheritdoc} */
    public function getApiProperties($className): array
    {
        return $this->decorated->getApiProperties($this->normalizeClassName($className));
    }

    /** {@inheritdoc} */
    public function getEntityProperties($className): array
    {
        return $this->decorated->getEntityProperties($this->normalizeClassName($className));
    }

    private function normalizeClassName(string $className): string
    {
        $manager = $this->registry->getManagerForClass($className);

        if (null === $manager) {
            throw MapperException::unsupportedClass($className);
        }

        $metadata = $manager->getClassMetadata($className);

        return $metadata->getName();
    }
}
