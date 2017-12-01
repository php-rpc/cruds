<?php

namespace ScayTrase\Api\Cruds\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Liip\FunctionalTestBundle\Test\WebTestCase as LiipTestCase;

abstract class WebTestCase extends LiipTestCase
{
    protected static function getKernelClass()
    {
        return KernelProvider::getClass();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->getContainer()->get('doctrine.orm.default_entity_manager');
    }
}
