<?php

namespace ScayTrase\Api\Cruds\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use ScayTrase\Api\Cruds\EntityProcessorInterface;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\Exception\EntityNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateController
{
    const ACTION = 'patchAction';

    /** @var  ObjectManager */
    private $manager;
    /** @var  EventDispatcherInterface */
    private $evm;
    /** @var  ObjectRepository */
    private $repository;
    /** @var  EntityProcessorInterface */
    private $processor;

    /**
     * UpdateController constructor.
     *
     * @param ObjectRepository         $repository
     * @param EntityProcessorInterface $processor
     * @param ObjectManager            $manager
     * @param EventDispatcherInterface $evm
     */
    public function __construct(
        ObjectRepository $repository,
        EntityProcessorInterface $processor,
        ObjectManager $manager,
        EventDispatcherInterface $evm = null
    ) {
        $this->manager    = $manager;
        $this->repository = $repository;
        $this->processor  = $processor;
        $this->evm        = $evm ?: new EventDispatcher();
    }

    /**
     * @param mixed $identifier
     * @param mixed $data
     *
     * @return object
     * @throws EntityNotFoundException
     */
    public function patchAction($identifier, $data)
    {
        $entity = $this->repository->find($identifier);

        if (!$entity) {
            throw EntityNotFoundException::byIdentifier($identifier);
        }

        $this->evm->dispatch(CrudEvents::READ, new CollectionCrudEvent([$entity]));
        $entity = $this->processor->updateEntity($entity, $data);
        $this->evm->dispatch(CrudEvents::UPDATE, new CollectionCrudEvent([$entity]));
        $this->manager->flush();

        return $entity;
    }
}
