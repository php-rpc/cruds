<?php

namespace ScayTrase\Api\Cruds\Adaptors\Symfony;

use ScayTrase\Api\Cruds\EntityProcessorInterface;
use ScayTrase\Api\Cruds\Exception\EntityProcessingException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;

final class FormProcessor implements EntityProcessorInterface
{
    /** @var  FormFactoryInterface */
    private $factory;
    /** @var  string|FormTypeInterface */
    private $form;
    /** @var  array */
    private $options = [];

    /**
     * FormProcessor constructor.
     *
     * @param FormFactoryInterface     $factory
     * @param string|FormTypeInterface $form
     * @param array                    $options
     */
    public function __construct(FormFactoryInterface $factory, $form, array $options = [])
    {
        $this->factory = $factory;
        $this->form    = $form;
        $this->options = $options;
    }

    /** {@inheritdoc} */
    public function updateEntity($entity, $data)
    {
        $form = $this->factory->create($this->form, $entity, $this->options);

        $form->submit($data, false);

        if (!$form->isValid()) {
            throw EntityProcessingException::invalidDataSubmitted((string)$form->getErrors(true), $data);
        }

        return $form->getData();
    }
}
