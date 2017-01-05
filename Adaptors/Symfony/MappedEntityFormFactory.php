<?php

namespace ScayTrase\Api\Cruds\Adaptors\Symfony;

use ScayTrase\Api\Cruds\Exception\EntityProcessingException;
use ScayTrase\Api\Cruds\PropertyMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

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

    /**
     * @param $className
     *
     * @return FormInterface
     *
     * @throws EntityProcessingException
     */
    public function createFormForClass($className)
    {
        $form = $this->factory->create(
            FormType::class,
            null,
            [
                'data_class' => $className,
            ]
        );

        try {
            foreach ($this->mapper->getApiProperties($className) as $apiProperty) {
                $form->add(
                    $this->factory->createForProperty(
                        $className,
                        $this->mapper->getEntityProperty($className, $apiProperty),
                        null,
                        ['auto_initialize' => false]
                    )
                );
            }
        } catch (\Exception $exception) {
            throw new EntityProcessingException(
                sprintf(
                    'Cannot create form for class %s: %s',
                    $className,
                    $exception->getMessage()
                ),
                $exception->getCode(),
                $exception
            );
        }

        return $form;
    }
}
