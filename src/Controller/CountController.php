<?php

namespace ScayTrase\Api\Cruds\Controller;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use ScayTrase\Api\Cruds\CriteriaConfiguratorInterface;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\Exception\CriteriaConfigurationException;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CountController
{
    const ACTION = 'countAction';

    /** @var string */
    private $fqcn;
    /** @var CriteriaConfiguratorInterface */
    private $configurator;
    /** @var  Selectable */
    private $repository;
    /** @var EventDispatcherInterface */
    private $evm;

    /**
     * CountController constructor.
     *
     * @param string                        $fqcn
     * @param Selectable                    $repository
     * @param CriteriaConfiguratorInterface $configurator
     * @param EventDispatcherInterface      $evm
     */
    public function __construct(
        $fqcn,
        Selectable $repository,
        CriteriaConfiguratorInterface $configurator,
        EventDispatcherInterface $evm
    ) {
        $this->fqcn         = $fqcn;
        $this->configurator = $configurator;
        $this->repository   = $repository;
        $this->evm          = $evm;
    }

    /**
     * Performs counting search of entities and returns count
     *
     * @param array $criteria
     *
     * @return integer
     * @throws CriteriaConfigurationException
     */
    public function countAction(array $criteria = [])
    {
        $queryCriteria = new Criteria();

        $this->configurator->configure($this->fqcn, $queryCriteria, $criteria);
        $count = $this->repository->matching($queryCriteria)->count();

        $this->evm->dispatch(CrudEvents::COUNT, new Event());

        return $count;
    }
}
