<?php

namespace ScayTrase\Api\Cruds;

use Doctrine\ORM\QueryBuilder;
use ScayTrase\Api\Cruds\Exception\FilterException;

interface CriteriaConfiguratorInterface
{
    /**
     * Updates builder according to filter configuration
     *
     * @param QueryBuilder $builder
     * @param mixed        $criteria
     *
     * @throws FilterException
     */
    public function configure(QueryBuilder $builder, $criteria);
}
