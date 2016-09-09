<?php

namespace ScayTrase\Api\Cruds\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteController
{
    const ACTION = 'deleteAction';

    /** @var  ObjectRepository */
    private $repository;
    /** @var  ObjectManager */
    private $manager;
    /** @var  EventDispatcherInterface */
    private $evm;

    /**
     * DeleteController constructor.
     *
     * @param ObjectRepository         $repository
     * @param ObjectManager            $manager
     * @param EventDispatcherInterface $evm
     */
    public function __construct(
        ObjectRepository $repository,
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
        $this->manager->remove($entity);

        $this->evm->dispatch(CrudEvents::DELETE, new CollectionCrudEvent([$entity]));
        $this->manager->flush();
    }
}
