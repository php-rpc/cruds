<?php

namespace ScayTrase\Api\Cruds\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfigurationTest extends KernelTestCase
{
    use CrudsTestCaseTrait;

    public function testConfigurationParsing()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();
        self::assertNotNull($container);
    }
}
