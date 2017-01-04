<?php

namespace ScayTrase\Api\Cruds;

final class LoadingReferenceProvider extends AbstractReferenceProvider
{
    /** {@inheritdoc} */
    protected function getReference($fqcn, $identifier)
    {
        return $this->getRegistry()->getManagerForClass($fqcn)->find($fqcn, $identifier);
    }
}
