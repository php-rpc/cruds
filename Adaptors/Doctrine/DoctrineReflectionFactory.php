<?php

namespace ScayTrase\Api\Cruds\Adaptors\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Registry;
use ScayTrase\Api\Cruds\Factory\ReflectionConstructorFactory;
use ScayTrase\Api\Cruds\ObjectFactoryInterface;

final class DoctrineReflectionFactory implements ObjectFactoryInterface
{
    /** @var  ObjectFactoryInterface */
    private $factory;

    /**
     * DoctrineReflectionFactory constructor.
     *
     * @param Registry $registry
     * @param string   $class
     * @param array    $args
     */
    public function __construct(Registry $registry, $class, array $args)
    {
        $this->factory = new ReflectionConstructorFactory(
            $registry->getManagerForClass($class)->getClassMetadata($class)->getName(),
            $args
        );
    }

    /** {@inheritdoc} */
    public function createObject($data)
    {
        return $this->factory->createObject($data);
    }
}
