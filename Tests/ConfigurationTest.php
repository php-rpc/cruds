<?php

namespace ScayTrase\Api\Cruds\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfigurationTest extends KernelTestCase
{
    use CrudsTestCaseTrait;

    /**
     * @dataProvider getKernelClasses
     * @param $kernel
     */
    public function testConfigurationParsing($kernel)
    {
        self::setKernelClass($kernel);
        self::bootKernel();

        $container = self::$kernel->getContainer();
        self::assertNotNull($container);
    }
}
