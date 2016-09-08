<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use ScayTrase\Api\Cruds\EntityProcessorInterface;

final class RelationAwareProcessorDecorator implements EntityProcessorInterface
{
    /** @var  ManagerRegistry */
    private $registry;
    /** @var  EntityProcessorInterface */
    private $processor;

    /**
     * RelationAwareProcessorDecorator constructor.
     *
     * @param EntityProcessorInterface $processor
     * @param ManagerRegistry          $registry
     */
    public function __construct(EntityProcessorInterface $processor, ManagerRegistry $registry)
    {
        $this->registry  = $registry;
        $this->processor = $processor;
    }

    /** {@inheritdoc} */
    public function updateEntity($entity, $data)
    {
        $class = get_class($entity);

        $manager = $this->registry->getManagerForClass($class);

        if (null !== $manager) {
            $metadata = $manager->getClassMetadata($class);

            foreach ($data as $property => &$value) {
                if (null === $value) {
                    continue;
                }

                if ($metadata->hasAssociation($property)) {
                    $assoc = $metadata->getAssociationTargetClass($property);

                    if ($manager instanceof EntityManagerInterface) {
                        $value = $manager->getReference($assoc, $value);
                    } else {
                        $value = $manager->find($assoc, $value);
                    }
                }
            }
        }

        return $this->processor->updateEntity($entity, $data);
    }
}
