<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller\Create;

use ScayTrase\Api\Cruds\Adaptors\Symfony\FormProcessor;
use ScayTrase\Api\Cruds\EntityProcessorInterface;
use ScayTrase\Api\Cruds\Tests\Fixtures\Form\AbcFormType;
use ScayTrase\Api\Cruds\Tests\Unit\Controller\CreateControllerTest;
use Symfony\Component\Form\FormFactoryBuilder;

final class FormProcessorTest extends CreateControllerTest
{

    /** {@inheritdoc} */
    protected function createProcessor(string $fqcn): EntityProcessorInterface
    {
        $builder = new FormFactoryBuilder();
        $factory = $builder->getFormFactory();

        return new FormProcessor(AbcFormType::class, [], $factory);
    }
}
