<?php

namespace ScayTrase\Api\Cruds\Adaptors\Symfony;

use ScayTrase\Api\Cruds\EntityProcessorInterface;

final class AutoFormProcessor implements EntityProcessorInterface
{
    /** @var  MappedEntityFormFactory */
    private $factory;

    /**
     * AutoFormProcessor constructor.
     *
     * @param MappedEntityFormFactory $factory
     */
    public function __construct(MappedEntityFormFactory $factory)
    {
        $this->factory = $factory;
    }

    /** {@inheritdoc} */
    public function updateEntity($entity, $data)
    {
        $processor = new FormProcessor($this->factory->createFormForClass(get_class($entity)));

        return $processor->updateEntity($entity, $data);
    }
}
