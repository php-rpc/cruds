<?php

namespace ScayTrase\Api\Cruds\Tests\Unit;

use ScayTrase\Api\Cruds\Tests\KernelProvider;
use Symfony\Component\BrowserKit\Client;

trait StaticDbTestTrait
{
    /** @var Client */
    private static $client;

    public static function setUpBeforeClass()
    {
        self::$class = KernelProvider::getClass();
        self::bootKernel();
        self::configureDb();
        self::$client = self::createClient();
    }

    public function setUp()
    {
    }

    public function tearDown()
    {
    }
}
