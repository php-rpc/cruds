<?php

namespace ScayTrase\Api\Cruds\Tests;

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
