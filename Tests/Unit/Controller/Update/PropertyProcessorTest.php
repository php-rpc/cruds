<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller\Update;

use ScayTrase\Api\Cruds\PropertyAccessProcessor;
use ScayTrase\Api\Cruds\Tests\Unit\Controller\UpdateControllerTest;

class PropertyProcessorTest extends UpdateControllerTest
{
    /** {@inheritdoc} */
    protected function createProcessor($fqcn)
    {
        return new PropertyAccessProcessor();
    }
}
