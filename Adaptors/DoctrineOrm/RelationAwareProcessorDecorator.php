<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\ORM\EntityManagerInterface;
use ScayTrase\Api\Cruds\EntityProcessorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

final class RelationAwareProcessorDecorator implements EntityProcessorInterface
{
    /** @var  RegistryInterface */
    private $registry;
    /** @var  EntityProcessorInterface */
    private $processor;

    /**
     * RelationAwareProcessorDecorator constructor.
     *
     * @param EntityProcessorInterface $processor
     * @param RegistryInterface        $registry
     */
    public function __construct(EntityProcessorInterface $processor, RegistryInterface $registry)
    {
        $this->registry  = $registry;
        $this->processor = $processor;
    }

    /** {@inheritdoc} */
    public function updateEntity($entity, $data)
    {
        $class = get_class($entity);

        $manager  = $this->registry->getManagerForClass($class);
        $metadata = $manager->getClassMetadata($class);

        foreach ($data as $property => &$value) {
            if ($metadata->hasAssociation($property)) {
                $assoc = $metadata->getAssociationTargetClass($property);

                if ($manager instanceof EntityManagerInterface) {
                    $value = $manager->getReference($assoc, $value);
                } else {
                    $value = $manager->find($assoc, $value);
                }
            }
        }

        return $this->processor->updateEntity($entity, $data);
    }
}
