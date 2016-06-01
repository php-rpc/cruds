<?php

namespace ScayTrase\Api\Cruds\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use ScayTrase\Api\Cruds\EntityProcessorInterface;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\Event\EntityCrudEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UpdateController
{
    const ACTION = 'patchAction';

    /** @var  ObjectManager */
    private $manager;
    /** @var  EventDispatcherInterface */
    private $evm;
    /** @var  EntityRepository */
    private $repository;
    /** @var  EntityProcessorInterface */
    private $processor;

    /**
     * UpdateController constructor.
     *
     * @param EntityRepository         $repository
     * @param EntityProcessorInterface $processor
     * @param ObjectManager            $manager
     * @param EventDispatcherInterface $evm
     */
    public function __construct(
        EntityRepository $repository,
        EntityProcessorInterface $processor,
        ObjectManager $manager,
        EventDispatcherInterface $evm = null
    ) {
        $this->manager    = $manager;
        $this->repository = $repository;
        $this->processor  = $processor;
        $this->evm        = $evm ?: new EventDispatcher();
    }


    public function patchAction($identifier, $data)
    {
        $entity = $this->repository->find($identifier);

        $this->evm->dispatch(CrudEvents::PRE_UPDATE, new EntityCrudEvent([$entity]));
        $entity = $this->processor->updateEntity($entity, $data);
        $this->evm->dispatch(CrudEvents::POST_UPDATE, new EntityCrudEvent([$entity]));

        $this->manager->flush();

        return $entity;
    }
}
