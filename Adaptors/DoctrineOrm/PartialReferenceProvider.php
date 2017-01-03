<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use ScayTrase\Api\Cruds\ReferenceProviderInterface;

final class PartialReferenceProvider implements ReferenceProviderInterface
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

        $manager = $this->registry->getManagerForClass($fqcn);
        if (!$manager instanceof EntityManagerInterface) {
            return $manager->find($target, $identifier);
        }

        return $manager->getPartialReference($target, $identifier);
    }
}
