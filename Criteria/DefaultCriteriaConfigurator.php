<?php

namespace ScayTrase\Api\Cruds\Criteria;

use Doctrine\Common\Collections\Criteria;
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

    /** {@inheritdoc} */
    public function configure($fqcn, Criteria $criteria, $arguments)
    {
        $arguments = array_replace($this->defaults, $arguments);
        $this->configurator->configure($fqcn, $criteria, $arguments);
    }
}
