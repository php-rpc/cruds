<?php

namespace ScayTrase\Api\Cruds\Adaptors\Symfony;

use ScayTrase\Api\Cruds\EntityProcessorInterface;
use Symfony\Component\Form\FormFactoryInterface;

final class AutoFormProcessor implements EntityProcessorInterface
{
    /** @var  MappedEntityFormFactory */
    private $factory;
    /** @var  FormFactoryInterface */
    private $formFactory;

    /**
     * AutoFormProcessor constructor.
     *
     * @param FormFactoryInterface    $formFactory
     * @param MappedEntityFormFactory $factory
     */
    public function __construct(FormFactoryInterface $formFactory, MappedEntityFormFactory $factory)
    {
        $this->factory     = $factory;
        $this->formFactory = $formFactory;
    }

    /** {@inheritdoc} */
    public function updateEntity($entity, $data)
    {
        $processor = new FormProcessor($this->formFactory, $this->factory->createFormForClass(get_class($entity)));

        return $processor->updateEntity($entity, $data);
    }
}
