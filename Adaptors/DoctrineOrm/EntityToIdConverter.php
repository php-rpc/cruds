<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;

final class EntityToIdConverter
{
    /** @var  ManagerRegistry */
    private $registry;

    /**
     * EntityToIdConverter constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param $entity
     *
     * @return mixed|array
     */
    public function convert($entity)
    {
        $class    = get_class($entity);
        $metadata = $this->registry->getManagerForClass($class)->getClassMetadata($class);

        $ids = $metadata->getIdentifierValues($entity);

        if (!$metadata instanceof ClassMetadata || $metadata->isIdentifierComposite) {
            return $ids;
        }

        return array_shift($ids);
    }
}
