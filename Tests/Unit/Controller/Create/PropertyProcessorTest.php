<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller\Create;

use ScayTrase\Api\Cruds\EntityProcessorInterface;
use ScayTrase\Api\Cruds\PropertyAccessProcessor;
use ScayTrase\Api\Cruds\Tests\Unit\Controller\CreateControllerTest;

final class PropertyProcessorTest extends CreateControllerTest
{
    /** {@inheritdoc} */
    protected function createProcessor(string $fqcn): EntityProcessorInterface
    {
        return new PropertyAccessProcessor();
    }
}
