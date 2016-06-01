<?php

namespace ScayTrase\Api\Cruds\Controller;

use Doctrine\ORM\EntityRepository;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\Event\EntityCrudEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ReadController
{
    const ACTION = 'getAction';

    /** @var  EntityRepository */
    private $repository;
    /** @var  EventDispatcherInterface */
    private $evm;

    /**
     * ReadController constructor.
     *
     * @param EntityRepository         $repository
     * @param EventDispatcherInterface $evm
     */
    public function __construct(EntityRepository $repository, EventDispatcherInterface $evm = null)
    {
        $this->repository = $repository;
        $this->evm        = $evm ?: new EventDispatcher();
    }

    /**
     * Returns the entity by given identifiers
     *
     * @param mixed $identifier
     *
     * @return null|object
     */
    public function getAction($identifier)
    {
        $entity = $this->repository->find($identifier);

        $this->evm->dispatch(CrudEvents::READ, new EntityCrudEvent([$entity]));

        return $entity;
    }
}
