<?php

namespace ScayTrase\Api\Cruds\Filter;

use Doctrine\ORM\QueryBuilder;
use ScayTrase\Api\Cruds\CriteriaConfiguratorInterface;

final class DefaultCriteriaConfigurator implements CriteriaConfiguratorInterface
{
    /** @var  CriteriaConfiguratorInterface */
    private $configurator;

    private $defaults = [];

    /**
     * DefaultCriteriaConfigurator constructor.
     *
     * @param CriteriaConfiguratorInterface $configurator
     * @param array                         $defaults
     */
    public function __construct(CriteriaConfiguratorInterface $configurator, array $defaults)
    {
        $this->configurator = $configurator;
        $this->defaults     = $defaults;
    }

    /**
     * Updates builder according to filter configuration
     *
     * @param QueryBuilder $builder
     * @param array        $criteria
     *
     */
    public function configure(QueryBuilder $builder, $criteria)
    {
        $criteria = array_replace($this->defaults, $criteria);
        $this->configurator->configure($builder, $criteria);
    }
}
