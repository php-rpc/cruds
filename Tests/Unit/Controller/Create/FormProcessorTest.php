<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller\Create;

use ScayTrase\Api\Cruds\Adaptors\Symfony\FormProcessor;
use ScayTrase\Api\Cruds\Tests\Unit\Controller\CreateControllerTest;
use Symfony\Component\Form\FormFactoryBuilder;

class FormProcessorTest extends CreateControllerTest
{

    /** {@inheritdoc} */
    protected function createProcessor($fqcn)
    {
        $builder = new FormFactoryBuilder();
        $factory = $builder->getFormFactory();

        return new FormProcessor(\ScayTrase\Api\Cruds\Tests\Fixtures\Form\AbcFormType::class, [], $factory);
    }
}
