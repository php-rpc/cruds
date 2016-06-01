<?php

namespace ScayTrase\Api\Cruds;

use Doctrine\ORM\QueryBuilder;
use ScayTrase\Api\Cruds\Exception\FilterException;

interface EntityFilterInterface
{
    /**
     * Updates builder according to filter configuration
     *
     * @param QueryBuilder $builder
     * @param array        $filters
     *
     * @throws FilterException
     */
    public function filter(QueryBuilder $builder, array &$filters);
}
