<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as ClassMetadataInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
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

        if (null === $rawValue) {
            return null;
        }

        $metadata = $this->getObjectMetadata($object, $attribute);
        if (null === $metadata) {
            return $rawValue;
        }

        $assocMetadata = $this->getAssocMetadata($metadata, $attribute);

        if ($metadata->isSingleValuedAssociation($attribute)) {
            return $this->getAssociationValue($assocMetadata, $rawValue);
        }

        $result = [];
        foreach ($rawValue as $item) {
            $result[] = $this->getAssociationValue($assocMetadata, $item);
        }

        return $result;
    }

    protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = [])
    {
        if (null === $value) {
            return parent::setAttributeValue($object, $attribute, $value, $format, $context);
        }

        $rawValue = $value;

        $metadata = $this->getObjectMetadata($object, $attribute);
        if (null === $metadata) {
            return $rawValue;
        }

        $assocMetadata = $this->getAssocMetadata($metadata, $attribute);

        $manager = $this->registry->getManagerForClass($assocMetadata->getName());
        if ($metadata->isSingleValuedAssociation($attribute)) {
            $value = $this->getObjectByValue($manager, $assocMetadata, $rawValue);

            return parent::setAttributeValue($object, $attribute, $value, $format, $context);
        }

        $objects = [];
        foreach ($rawValue as $item) {
            $objects[] = $this->getObjectByValue($manager, $assocMetadata, $item);
        }

        return parent::setAttributeValue($object, $attribute, new ArrayCollection($objects), $format, $context);
    }

    /**
     * @param ObjectManager          $manager
     * @param ClassMetadataInterface $assocMetadata
     * @param                        $rawValue
     *
     * @return object
     */
    protected function getObjectByValue(ObjectManager $manager, ClassMetadataInterface $assocMetadata, $rawValue)
    {
        if ($manager instanceof EntityManagerInterface) {
            return $manager->getReference($assocMetadata->getName(), $rawValue);
        }

        return $manager->find($assocMetadata->getName(), $rawValue);
    }

    /**
     * @param $assocMetadata
     * @param $rawValue
     *
     * @return mixed
     */
    private function getAssociationValue(ClassMetadataInterface $assocMetadata, $rawValue)
    {
        $identifier = $assocMetadata->getIdentifierValues($rawValue);

        if (!$assocMetadata instanceof ClassMetadata || $assocMetadata->isIdentifierComposite) {
            return $identifier;
        }

        return array_shift($identifier);
    }

    private function getObjectMetadata($object, $attribute)
    {
        $class = get_class($object);

        $manager = $this->registry->getManagerForClass($class);
        if (null === $manager) {
            return null;
        }

        $metadata = $manager->getClassMetadata($class);
        if (!$metadata->hasAssociation($attribute)) {
            return null;
        }

        return $metadata;
    }

    private function getAssocMetadata(ClassMetadataInterface $metadata, $attribute)
    {
        $assoc        = $metadata->getAssociationTargetClass($attribute);
        $assocManager = $this->registry->getManagerForClass($assoc);

        if (null === $assocManager) {
            throw new \LogicException(
                $metadata->getName().
                '::$'.
                $attribute.
                ' references non-existent managed entity'
            );
        }

        return $assocManager->getClassMetadata($assoc);
    }
}
