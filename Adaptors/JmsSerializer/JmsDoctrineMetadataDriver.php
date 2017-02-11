<?php

namespace ScayTrase\Api\Cruds\Adaptors\JmsSerializer;

use Doctrine\Common\Persistence\Mapping\ClassMetadata as DoctrineClassMetadata;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\Driver\AbstractDoctrineTypeDriver;
use JMS\Serializer\Metadata\PropertyMetadata;

final class JmsDoctrineMetadataDriver extends AbstractDoctrineTypeDriver
{
    /** {@inheritdoc} */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        /** @var $classMetadata ClassMetadata */
        $classMetadata = $this->delegate->loadMetadataForClass($class);

        // Abort if the given class is not a mapped entity
        if (!$doctrineMetadata = $this->tryLoadingDoctrineMetadata($class->name)) {
            return $classMetadata;
        }

        $this->setDiscriminator($doctrineMetadata, $classMetadata);

        // We base our scan on the internal driver's property list so that we
        // respect any internal white/blacklisting like in the AnnotationDriver
        foreach ($classMetadata->propertyMetadata as $key => $propertyMetadata) {
            if ($this->hideProperty($doctrineMetadata, $propertyMetadata)) {
                unset($classMetadata->propertyMetadata[$key]);
            }

            $this->setPropertyType($doctrineMetadata, $propertyMetadata);
        }

        return $classMetadata;
    }

    protected function setPropertyType(DoctrineClassMetadata $doctrineMetadata, PropertyMetadata $propertyMetadata)
    {
        parent::setPropertyType($doctrineMetadata, $propertyMetadata);

        if (!$doctrineMetadata->hasAssociation($propertyMetadata->name)) {
            return;
        }

        $template = '%s<%s>';

        if (null === $propertyMetadata->type['name']) {
            $propertyMetadata->type['name'] = $doctrineMetadata->getAssociationTargetClass($propertyMetadata->name);
        }

        // Makes the My\Ns\TargetEntity be DoctrineAssociation<My\Ns\TargetEntity>
        // And ArrayCollection<My\Ns\TargetEntity> be DoctrineAssociation<ArrayCollection<My\Ns\TargetEntity>>
        $propertyMetadata->setType(
            sprintf(
                $template,
                JmsDoctrineHandler::TYPE,
                $propertyMetadata->type['name']
            )
        );
    }
}
