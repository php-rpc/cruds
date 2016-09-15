<?php

namespace ScayTrase\Api\Cruds\Criteria;

use Doctrine\Common\Collections\Criteria;
use ScayTrase\Api\Cruds\CriteriaConfiguratorInterface;
use ScayTrase\Api\Cruds\Exception\CriteriaConfigurationException;
use ScayTrase\Api\Cruds\Exception\NestedConfiguratorException;

final class NestedCriteriaConfigurator implements CriteriaConfiguratorInterface
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
        if (!is_array($arguments)) {
            throw NestedConfiguratorException::invalidType('array', gettype($arguments));
        }

        $diff = array_keys(array_diff_key($arguments, $this->filters));
        if (count($diff) !== 0) {
            throw NestedConfiguratorException::unknown($diff);
        }

        foreach ((array)$arguments as $filter => $item) {
            try {
                $this->filters[$filter]->configure($fqcn, $criteria, $item);
            } catch (CriteriaConfigurationException $exception) {
                throw NestedConfiguratorException::invalidNesting($filter, $exception);
            }
        }
    }
}
