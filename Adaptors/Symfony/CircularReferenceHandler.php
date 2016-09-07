<?php

namespace ScayTrase\Api\Cruds\Adaptors\Symfony;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\RegistryInterface;

final class CircularReferenceHandler
{
    /** @var  RegistryInterface */
    private $registry;

    /**
     * CircularReferenceHandler constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
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
