<?php

namespace ScayTrase\Api\Cruds\Filter;

use Doctrine\ORM\QueryBuilder;
use ScayTrase\Api\Cruds\CriteriaConfiguratorInterface;

final class ChainCriteriaConfigurator implements CriteriaConfiguratorInterface
{
    /** @var CriteriaConfiguratorInterface[] */
    private $filters = [];

    /**
     * ChainFilter constructor.
     *
     * @param CriteriaConfiguratorInterface[] $filters
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /** {@inheritdoc} */
    public function configure(QueryBuilder $builder, $criteria)
    {
        foreach ($this->filters as $filter) {
            $filter->configure($builder, $criteria);
        }
    }
}
