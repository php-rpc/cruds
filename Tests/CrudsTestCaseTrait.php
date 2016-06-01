<?php

namespace ScayTrase\Api\Cruds\Tests;

use ScayTrase\Api\Cruds\Tests\Fixtures\TestKernel;

trait CrudsTestCaseTrait
{
    protected static function getKernelClass()
    {
        return TestKernel::class;
    }
}
