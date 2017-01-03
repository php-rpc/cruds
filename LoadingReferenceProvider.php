<?php

namespace ScayTrase\Api\Cruds;

use Doctrine\Common\Persistence\ManagerRegistry;

final class LoadingReferenceProvider implements ReferenceProviderInterface
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * PartialReferenceProvider constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /** {@inheritdoc} */
    public function getEntityReference($fqcn, $property, $identifier)
    {
        $metadata = $this->registry->getManagerForClass($fqcn)->getClassMetadata($fqcn);

        if (!$metadata->hasAssociation($property)) {
            return $identifier;
        }

        $target = $metadata->getAssociationTargetClass($property);

        return $this->registry->getManagerForClass($fqcn)->getPartialReference($target, $identifier);
    }
}
