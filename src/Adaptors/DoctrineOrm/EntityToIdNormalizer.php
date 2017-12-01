<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

final class EntityToIdNormalizer
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
    public function normalize($entity)
    {
        $class    = get_class($entity);
        $metadata = $this->registry->getManagerForClass($class)->getClassMetadata($class);

        $ids = $metadata->getIdentifierValues($entity);

        if (!$metadata instanceof ClassMetadata || $metadata->isIdentifierComposite) {
            return $ids;
        }

        return array_shift($ids);
    }

    /**
     * @param mixed|array $identifier
     * @param string      $class
     *
     * @return object
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function denormalize($identifier, $class)
    {
        $manager = $this->registry->getManagerForClass($class);

        if (null === $manager) {
            throw new \RuntimeException('Not supported class ' . $class);
        }

        if ($manager instanceof EntityManagerInterface) {
            return $manager->getReference($class, $identifier);
        }

        return $manager->find($class, $identifier);
    }
}
