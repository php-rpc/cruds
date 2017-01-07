<?php

namespace ScayTrase\Api\Cruds;

use Doctrine\Common\Persistence\ManagerRegistry;

abstract class AbstractReferenceProvider implements ReferenceProviderInterface
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
    final public function getEntityReference($fqcn, $property, $identifier)
    {
        $metadata = $this->registry->getManagerForClass($fqcn)->getClassMetadata($fqcn);

        if (!$metadata->hasAssociation($property)) {
            return $identifier;
        }

        $target = $metadata->getAssociationTargetClass($property);

        $targetMetadata = $this->registry->getManagerForClass($target)->getClassMetadata($target);

        if (is_array($identifier)) {
            $isNonCompositeIdentifier   = count($targetMetadata->getIdentifier()) === 1;
            $isScalarArrayOfIdentifiers = array_keys($identifier) === range(0, count($identifier) - 1);
            if ($isNonCompositeIdentifier || $isScalarArrayOfIdentifiers) {
                $references = [];
                foreach ($identifier as $item) {
                    $references[] = $this->getReference($target, $item);
                }

                return $references;
            }
        }

        return $this->getReference($target, $identifier);
    }

    /**
     * Returns the reference of class
     *
     * @param string $fqcn
     * @param mixed  $identifier
     *
     * @return object
     */
    abstract protected function getReference($fqcn, $identifier);

    /**
     * @return ManagerRegistry
     */
    protected function getRegistry()
    {
        return $this->registry;
    }
}
