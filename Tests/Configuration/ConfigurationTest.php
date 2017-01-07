<?php

namespace ScayTrase\Api\Cruds\Tests\Configuration;

use ScayTrase\Api\Cruds\Tests\AbstractCrudsWebTest;

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
