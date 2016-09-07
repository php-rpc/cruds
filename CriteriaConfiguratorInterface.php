<?php

namespace ScayTrase\Api\Cruds;

use Doctrine\Common\Collections\Criteria;
use ScayTrase\Api\Cruds\Exception\CriteriaConfigurationException;

interface CriteriaConfiguratorInterface
{
    /**
     * Updates builder according to filter configuration
     *
     * @param string   $fqcn
     * @param Criteria $criteria
     * @param mixed    $arguments
     *
     * @throws CriteriaConfigurationException
     */
    public function configure($fqcn, Criteria $criteria, $arguments);
}
