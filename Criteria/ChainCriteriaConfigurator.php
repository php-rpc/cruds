<?php

namespace ScayTrase\Api\Cruds\Criteria;

use Doctrine\Common\Collections\Criteria;
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
    public function configure($fqcn, Criteria $criteria, $arguments)
    {
        foreach ($this->filters as $filter) {
            $filter->configure($fqcn, $arguments, $arguments);
        }
    }
}
