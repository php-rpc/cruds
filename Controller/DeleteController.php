<?php

namespace ScayTrase\Api\Cruds\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\Event\EntityCrudEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteController
{
    const ACTION = 'deleteAction';

    /** @var  EntityRepository */
    private $repository;
    /** @var  ObjectManager */
    private $manager;
    /** @var  EventDispatcherInterface */
    private $evm;

    /**
     * DeleteController constructor.
     *
     * @param EntityRepository         $repository
     * @param ObjectManager            $manager
     * @param EventDispatcherInterface $evm
     */
    public function __construct(
        EntityRepository $repository,
        ObjectManager $manager,
        EventDispatcherInterface $evm = null
    ) {
        $this->repository = $repository;
        $this->manager    = $manager;
        $this->evm        = $evm ?: new EventDispatcher();
    }

    /**
     * Removes the entity by given identifiers
     *
     * @param mixed $identifier
     */
    public function deleteAction($identifier)
    {
        $entity = $this->repository->find($identifier);
        $this->evm->dispatch(CrudEvents::PRE_DELETE, new EntityCrudEvent([$entity]));
        $this->manager->remove($entity);
        $this->evm->dispatch(CrudEvents::POST_DELETE, new EntityCrudEvent([$entity]));
        $this->manager->flush();
    }
}
