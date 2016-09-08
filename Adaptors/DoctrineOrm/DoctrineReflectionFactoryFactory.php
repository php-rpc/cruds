<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Persistence\ManagerRegistry;
use ScayTrase\Api\Cruds\ObjectFactoryInterface;
use ScayTrase\Api\Cruds\ReflectionConstructorFactory;

final class DoctrineReflectionFactoryFactory
{
    /** @var  ManagerRegistry */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param string $class
     * @param array  $args
     *
     * @return ObjectFactoryInterface
     */
    public function create($class, array $args)
    {
        return new ReflectionConstructorFactory(
            $this->registry->getManagerForClass($class)->getClassMetadata($class)->getName(),
            $args
        );
    }
}
