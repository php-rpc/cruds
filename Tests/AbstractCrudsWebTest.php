<?php

namespace ScayTrase\Api\Cruds\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use ScayTrase\Api\Cruds\Tests\Fixtures\JmsSerializer\JmsTestKernel;
use ScayTrase\Api\Cruds\Tests\Fixtures\SymfonySerializer\SymfonyTestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractCrudsWebTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        self::$class = KernelProvider::getClass();
        parent::setUpBeforeClass();
    }

    public function setUp()
    {
        parent::setUp();
        self::bootKernel();
    }
}
