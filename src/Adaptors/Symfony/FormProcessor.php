<?php

namespace ScayTrase\Api\Cruds\Adaptors\Symfony;

use ScayTrase\Api\Cruds\EntityProcessorInterface;
use ScayTrase\Api\Cruds\Exception\EntityProcessingException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
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
     * @param string|FormTypeInterface  $form    Form instance or form class (factory required for class)
     * @param array                     $options Form options
     * @param FormFactoryInterface|null $factory Optional if form already provided
     *
     * @throws \LogicException
     */
    public function __construct($form, array $options = [], FormFactoryInterface $factory = null)
    {
        if (!$form instanceof FormInterface && !$factory) {
            throw new \LogicException('You should either provide instantiated form or factory');
        }

        $this->factory = $factory;
        $this->form    = $form;
        $this->options = $options;
    }

    /** {@inheritdoc} */
    public function updateEntity($entity, $data)
    {
        $form = $this->form;

        if (!$form instanceof FormInterface) {
            $form = $this->factory->create($this->form, $entity, $this->options);
        } else {
            $form->setData($entity);
        }

        $form->submit($data, false);

        if (!$form->isValid()) {
            throw EntityProcessingException::invalidDataSubmitted((string)$form->getErrors(true));
        }

        return $form->getData();
    }
}
