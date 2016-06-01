<?php

namespace ScayTrase\Api\Cruds\Filter;

use Doctrine\ORM\QueryBuilder;
use ScayTrase\Api\Cruds\EntityFilterInterface;
use ScayTrase\Api\Cruds\Exception\FilterException;

final class DefaultEntityFilter implements EntityFilterInterface
{
    private $defaults = [];

    /**
     * DefaultFilter constructor.
     *
     * @param array $defaults
     */
    public function __construct(array $defaults = [])
    {
        $this->defaults = $defaults;
    }

    /**
     * Updates builder according to filter configuration
     *
     * @param QueryBuilder $builder
     * @param array        $filters
     *
     * @throws FilterException
     */
    public function filter(QueryBuilder $builder, array &$filters)
    {
        $filters = array_replace($this->defaults, $filters);
    }
}
