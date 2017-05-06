<?php

namespace ScayTrase\Api\Cruds\Tests;

trait StaticKernelTestTrait
{
    public static function setUpBeforeClass()
    {
        self::$class = KernelProvider::getClass();
        self::bootKernel();
    }

    public function setUp()
    {
    }

    public function tearDown()
    {
    }
}
