<?php

namespace ScayTrase\Api\Cruds\Adaptors\JmsSerializer;

use Doctrine\Common\Persistence\ManagerRegistry;
use JMS\Serializer\Context;
use JMS\Serializer\VisitorInterface;
use ScayTrase\Api\Cruds\Adaptors\DoctrineOrm\EntityToIdConverter;

final class JmsDoctrineHandler
{
    const TYPE = 'DoctrineAssociation';

    /** @var  ManagerRegistry */
    private $registry;
    /** @var  EntityToIdConverter */
    private $converter;

    /**
     * JmsDoctrineHandler constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry  = $registry;
        $this->converter = new EntityToIdConverter($this->registry);
    }

    public function serializeRelation(VisitorInterface $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }

        if (is_array($relation)) {
            return array_map([$this, 'convertEntityToIds'], $relation);
        }

        return $this->converter->convert($relation);
    }

    public function deserializeRelation(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        // fixme
        throw new \BadMethodCallException('Not supported at the moment');
    }
}
