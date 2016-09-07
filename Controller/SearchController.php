<?php

namespace ScayTrase\Api\Cruds\Controller;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use ScayTrase\Api\Cruds\CriteriaConfiguratorInterface;
use ScayTrase\Api\Cruds\Exception\CriteriaConfigurationException;

final class SearchController
{
    const ACTION = 'findAction';

    /** @var string */
    private $fqcn;
    /** @var CriteriaConfiguratorInterface */
    private $filters = [];
    /** @var  Selectable */
    private $repository;

    /**
     * ReadController constructor.
     *
     * @param string                          $fqcn
     * @param Selectable                      $repository
     * @param CriteriaConfiguratorInterface[] $filters
     */
    public function __construct($fqcn, Selectable $repository, array $filters)
    {
        $this->fqcn       = $fqcn;
        $this->filters    = $filters;
        $this->repository = $repository;
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

        return $this->repository->matching($queryCriteria);
    }
}
