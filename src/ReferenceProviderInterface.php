<?php

namespace ScayTrase\Api\Cruds;

interface ReferenceProviderInterface
{
    /**
     * Returns a reference objects for comparing with Criteria
     *
     * @param string $fqcn
     * @param string $property
     * @param mixed  $identifier
     *
     * @return object|$identifier Reference or $identifier if property is not an association
     */
    public function getEntityReference($fqcn, $property, $identifier);
}
