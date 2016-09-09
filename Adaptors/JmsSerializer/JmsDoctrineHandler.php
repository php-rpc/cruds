<?php

namespace ScayTrase\Api\Cruds\Adaptors\JmsSerializer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\Serializer\Context;
use JMS\Serializer\VisitorInterface;

final class JmsDoctrineHandler
{
    const TYPE = 'DoctrineAssociation';

    /** @var  ManagerRegistry */
    private $registry;

    /**
     * JmsDoctrineHandler constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function serializeRelation(VisitorInterface $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }

        if (is_array($relation)) {
            return array_map([$this, 'convertEntityToIds'], $relation);
        }

        return $this->convertEntityToIds($relation);
    }

    public function deserializeRelation(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        // fixme
        throw new \BadMethodCallException('Not supported at the moment');
    }

    private function convertEntityToIds($entity)
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
