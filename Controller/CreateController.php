<?php

namespace ScayTrase\Api\Cruds\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use ScayTrase\Api\Cruds\EntityProcessorInterface;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\Exception\EntityProcessingException;
use ScayTrase\Api\Cruds\EntityFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CreateController
{
    const ACTION = 'createAction';

    /** @var  ObjectManager */
    private $manager;
    /** @var  EventDispatcherInterface */
    private $evm;
    /** @var  EntityFactoryInterface */
    private $factory;
    /** @var  EntityProcessorInterface */
    private $processor;

    /**
     * CreateController constructor.
     *
     * @param EntityProcessorInterface $processor
     * @param ObjectManager            $manager
     * @param EntityFactoryInterface   $factory
     * @param EventDispatcherInterface $evm
     */
    public function __construct(
        EntityProcessorInterface $processor,
        ObjectManager $manager,
        EntityFactoryInterface $factory,
        EventDispatcherInterface $evm = null
    ) {
        $this->manager   = $manager;
        $this->factory   = $factory;
        $this->processor = $processor;
        $this->evm       = $evm ?: new EventDispatcher();
    }


    /**
     * @param mixed $data
     *
     * @return object
     * @throws EntityProcessingException
     */
    public function createAction($data)
    {
        $object = $this->factory->createEntity($data);
        $entity = $this->processor->updateEntity($object, $data);

        $this->evm->dispatch(CrudEvents::READ, new CollectionCrudEvent([$entity]));
        $this->manager->persist($entity);
        $this->manager->flush();

        return $entity;
    }
}
