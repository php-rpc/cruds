<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Bundle\DoctrineBundle\Registry;
use ScayTrase\Api\Cruds\Factory\ReflectionConstructorFactory;
use ScayTrase\Api\Cruds\ObjectFactoryInterface;

final class DoctrineReflectionFactoryFactory
{
    /** @var  Registry */
    private $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
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
