<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;

final class CircularReferenceHandler
{
    /** @var  ManagerRegistry */
    private $registry;

    /**
     * CircularReferenceHandler constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function handle($object)
    {
        $class = get_class($object);

        $metadata   = $this->registry
            ->getManagerForClass($class)
            ->getClassMetadata($class);
        $identifier = $metadata->getIdentifierValues($object);

        if (!$metadata instanceof ClassMetadata || $metadata->isIdentifierComposite) {
            return $identifier;
        }

        return array_shift($identifier);
    }
}
