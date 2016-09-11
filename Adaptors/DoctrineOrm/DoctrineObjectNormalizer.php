<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class DoctrineObjectNormalizer extends ObjectNormalizer
{
    /** @var ManagerRegistry */
    private $registry;

    public function setRegistry(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && null !== $this->registry->getManagerForClass(get_class($data));
    }

    protected function getAttributeValue($object, $attribute, $format = null, array $context = [])
    {
        $rawValue = parent::getAttributeValue($object, $attribute, $format, $context);
        $accessor = new AssociationNormalizer($this->registry);

        return $accessor->normalize($rawValue, $object, $attribute);
    }

    protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = [])
    {
        $accessor = new AssociationNormalizer($this->registry);
        $value    = $accessor->denormalize($object, $attribute, $value);

        return parent::setAttributeValue($object, $attribute, $value, $format, $context);
    }
}
