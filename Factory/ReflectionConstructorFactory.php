<?php

namespace ScayTrase\Api\Cruds\Factory;

use ScayTrase\Api\Cruds\ObjectFactoryInterface;

final class ReflectionConstructorFactory implements ObjectFactoryInterface
{
    /** @var  string */
    private $className;
    /** @var  array */
    private $args = [];

    /**
     * ReflectionConstructorFactory constructor.
     *
     * @param string $className
     * @param array  $args
     */
    public function __construct($className, array $args = [])
    {
        $this->className = $className;
        $this->args      = $args;
    }

    /** {@inheritdoc} */
    public function createObject($data)
    {
        $reflection = new \ReflectionClass($this->className);

        return $reflection->newInstanceArgs($this->args);
    }
}
