<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller\Update;

use ScayTrase\Api\Cruds\EntityProcessorInterface;
use ScayTrase\Api\Cruds\PropertyAccessProcessor;
use ScayTrase\Api\Cruds\Tests\Unit\Controller\UpdateControllerTest;

final class PropertyProcessorTest extends UpdateControllerTest
{
    /** {@inheritdoc} */
    protected function createProcessor(string $fqcn): EntityProcessorInterface
    {
        return new PropertyAccessProcessor();
    }
}
