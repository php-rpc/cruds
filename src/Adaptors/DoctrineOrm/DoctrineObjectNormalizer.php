<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class DoctrineObjectNormalizer extends ObjectNormalizer
{
    /** @var ManagerRegistry */
    private $registry;

    public function setRegistry(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function supportsNormalization($data, $format = null)
    {
        return \is_object($data) && null !== $this->registry->getManagerForClass(\get_class($data));
    }
}
