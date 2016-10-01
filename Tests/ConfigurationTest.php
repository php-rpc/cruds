<?php

namespace ScayTrase\Api\Cruds\Tests;

use ScayTrase\Api\Cruds\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;

class ConfigurationTest extends AbstractCrudsWebTest
{
    /**
     * @dataProvider getKernelClasses
     *
     * @param $kernel
     */
    public function testConfigurationParsing($kernel)
    {
        self::createAndBootKernel($kernel);
        self::assertKernelBooted();
    }
}
