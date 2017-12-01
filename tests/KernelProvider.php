<?php

namespace ScayTrase\Api\Cruds\Tests;

use ScayTrase\Api\Cruds\Tests\Fixtures\JmsSerializer\JmsTestKernel;
use ScayTrase\Api\Cruds\Tests\Fixtures\SymfonySerializer\SymfonyTestKernel;

final class KernelProvider
{
    const ENV_VAR = 'SERIALIZER';

    private static $map = [
        'jms'     => JmsTestKernel::class,
        'symfony' => SymfonyTestKernel::class,
    ];

    public static function getClass()
    {
        if (false === ($alias = getenv(self::ENV_VAR))) {
            throw new \LogicException('Cannot obtain kernel alias from "' . self::ENV_VAR . '"" ENV variable');
        }

        if (!array_key_exists($alias, self::$map)) {
            throw new \LogicException('Invalid kernel alias: ' . $alias);
        }

        return self::$map[$alias];
    }
}
