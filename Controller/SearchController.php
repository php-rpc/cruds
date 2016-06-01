<?php

namespace ScayTrase\Api\Cruds\Controller;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use ScayTrase\Api\Cruds\CriteriaConfiguratorInterface;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\Event\EntityCrudEvent;
use ScayTrase\Api\Cruds\Exception\FilterException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SearchController
{
    const ACTION = 'findAction';

    /** @var CriteriaConfiguratorInterface */
    private $filters = [];
    /** @var  EntityRepository */
    private $repository;
    /** @var  EventDispatcherInterface */
    private $evm;

    /**
     * ReadController constructor.
     *
     * @param EntityRepository                $repository
     * @param CriteriaConfiguratorInterface[] $filters
     * @param EventDispatcherInterface        $evm
     */
    public function __construct(
        EntityRepository $repository,
        array $filters,
        EventDispatcherInterface $evm = null
    ) {
        $this->filters    = $filters;
        $this->repository = $repository;
        $this->evm        = $evm ?: new EventDispatcher();
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
     * @throws FilterException
     */
    public function findAction(array $criteria, array $order = [], $limit = 10, $offset = 0)
    {
        $builder = $this->repository->createQueryBuilder('e');

        $unknown = array_diff_key($criteria, $this->filters);

        if (count($unknown) > 0) {
            throw FilterException::unknown(array_keys($unknown));
        }

        foreach ($criteria as $filter => $item) {
            $this->filters[$filter]->configure($builder, $item);
        }

        foreach ($order as $key => $value) {
            $builder->addOrderBy(new Expr\Literal($key), strtolower($value) === 'asc' ? 'ASC' : 'DESC');
        }

        $builder->setMaxResults($limit);
        $builder->setFirstResult($offset);

        $entities = $builder->getQuery()->getResult();

        $this->evm->dispatch(CrudEvents::READ, new EntityCrudEvent($entities));

        return $entities;
    }
}
