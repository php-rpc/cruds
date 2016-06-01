<?php

namespace ScayTrase\Api\Cruds\Filter;

use Doctrine\ORM\QueryBuilder;
use ScayTrase\Api\Cruds\EntityFilterInterface;

final class ChainEntityFilter implements EntityFilterInterface
{
    /** @var EntityFilterInterface[] */
    private $filters = [];

    /**
     * ChainFilter constructor.
     *
     * @param EntityFilterInterface[] $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /** {@inheritdoc} */
    public function filter(QueryBuilder $builder, array &$filters)
    {
        foreach ($this->filters as $filter) {
            $filter->filter($builder, $filters);
        }
    }
}
