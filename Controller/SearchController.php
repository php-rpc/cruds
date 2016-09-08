<?php

namespace ScayTrase\Api\Cruds\Controller;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use ScayTrase\Api\Cruds\CriteriaConfiguratorInterface;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\Exception\CriteriaConfigurationException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SearchController
{
    const ACTION = 'findAction';

    /** @var string */
    private $fqcn;
    /** @var CriteriaConfiguratorInterface */
    private $filters = [];
    /** @var  Selectable */
    private $repository;
    /** @var EventDispatcherInterface */
    private $evm;

    /**
     * ReadController constructor.
     *
     * @param string                          $fqcn
     * @param Selectable                      $repository
     * @param CriteriaConfiguratorInterface[] $filters
     * @param EventDispatcherInterface        $evm
     */
    public function __construct($fqcn, Selectable $repository, array $filters, EventDispatcherInterface $evm)
    {
        $this->fqcn       = $fqcn;
        $this->filters    = $filters;
        $this->repository = $repository;
        $this->evm        = $evm;
    }

    /**
     * Performs search of entities and returns them
     *
     * @param array $criteria
     * @param array $order
     * @param int   $limit
     * @param int   $offset
     *
     * @return object[]
     * @throws CriteriaConfigurationException
     */
    public function findAction(array $criteria, array $order = [], $limit = 10, $offset = 0)
    {
        $queryCriteria = new Criteria(null, $order, $offset, $limit);

        $unknown = array_diff_key($criteria, $this->filters);

        if (count($unknown) > 0) {
            throw CriteriaConfigurationException::unknown(array_keys($unknown));
        }

        foreach ($criteria as $filter => $item) {
            $this->filters[$filter]->configure($this->fqcn, $queryCriteria, $item);
        }

        $entities = $this->repository->matching($queryCriteria);

        $this->evm->dispatch(CrudEvents::READ, new CollectionCrudEvent($entities));

        return $entities;
    }
}
