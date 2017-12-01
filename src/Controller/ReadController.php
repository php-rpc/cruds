<?php

namespace ScayTrase\Api\Cruds\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\Exception\EntityNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ReadController
{
    const ACTION = 'getAction';

    /** @var  ObjectRepository */
    private $repository;
    /** @var  EventDispatcherInterface */
    private $evm;

    /**
     * ReadController constructor.
     *
     * @param ObjectRepository         $repository
     * @param EventDispatcherInterface $evm
     */
    public function __construct(ObjectRepository $repository, EventDispatcherInterface $evm = null)
    {
        $this->repository = $repository;
        $this->evm        = $evm ?: new EventDispatcher();
    }

    /**
     * Returns the entity by given identifiers
     *
     * @param mixed $identifier
     *
     * @return object
     * @throws EntityNotFoundException
     */
    public function getAction($identifier)
    {
        $entity = $this->repository->find($identifier);

        if (!$entity) {
            throw EntityNotFoundException::byIdentifier($identifier);
        }

        $this->evm->dispatch(CrudEvents::READ, new CollectionCrudEvent([$entity]));

        return $entity;
    }
}
