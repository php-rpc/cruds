<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller\Create;

use ScayTrase\Api\Cruds\PropertyAccessProcessor;
use ScayTrase\Api\Cruds\Tests\Unit\Controller\CreateControllerTest;

class PropertyProcessorTest extends CreateControllerTest
{
    /** {@inheritdoc} */
    protected function createProcessor($fqcn)
    {
        return new PropertyAccessProcessor();
    }
}
