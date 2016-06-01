<?php

namespace ScayTrase\Api\Cruds\Adaptors\Symfony;

use ScayTrase\Api\Cruds\PropertyMapperInterface;
use Symfony\Component\Form\FormFactoryInterface;

final class MappedEntityFormFactory
{
    /** @var  FormFactoryInterface */
    private $factory;
    /** @var  PropertyMapperInterface */
    private $mapper;

    /**
     * MappedEntityFormFactory constructor.
     *
     * @param FormFactoryInterface    $factory
     * @param PropertyMapperInterface $mapper
     */
    public function __construct(FormFactoryInterface $factory, PropertyMapperInterface $mapper)
    {
        $this->factory = $factory;
        $this->mapper  = $mapper;
    }

    public function createFormForClass($className)
    {
        $form = $this->factory->create();

        foreach ($this->mapper->getApiProperties($className) as $apiProperty) {
            $form->add(
                $apiProperty,
                $this->factory->createForProperty(
                    $className,
                    $this->mapper->getObjectProperty($className, $apiProperty)
                )
            );
        }

        return $form;
    }
}
