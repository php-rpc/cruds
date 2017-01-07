<?php

namespace ScayTrase\Api\Cruds\Adaptors\DoctrineOrm;

use Doctrine\ORM\EntityManagerInterface;
use ScayTrase\Api\Cruds\AbstractReferenceProvider;

final class PartialReferenceProvider extends AbstractReferenceProvider
{
    protected function getReference($fqcn, $identifier)
    {
        $manager = $this->getRegistry()->getManagerForClass($fqcn);
        if (!$manager instanceof EntityManagerInterface) {
            return $manager->find($fqcn, $identifier);
        }

        return $manager->getPartialReference($fqcn, $identifier);
    }
}
