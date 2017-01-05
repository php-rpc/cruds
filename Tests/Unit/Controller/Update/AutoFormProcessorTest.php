<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller\Update;

use ScayTrase\Api\Cruds\Adaptors\Symfony\AutoFormProcessor;
use ScayTrase\Api\Cruds\Adaptors\Symfony\MappedEntityFormFactory;
use ScayTrase\Api\Cruds\Adaptors\Symfony\PhpDocTypeGuesser;
use ScayTrase\Api\Cruds\PublicPropertyMapper;
use ScayTrase\Api\Cruds\Tests\Unit\Controller\UpdateControllerTest;
use Symfony\Component\Form\FormFactoryBuilder;

class AutoFormProcessorTest extends UpdateControllerTest
{
    /** {@inheritdoc} */
    protected function createProcessor($fqcn)
    {
        $builder = new FormFactoryBuilder();
        $builder->addTypeGuesser(new PhpDocTypeGuesser());
        $factory = $builder->getFormFactory();

        return new AutoFormProcessor(new MappedEntityFormFactory($factory, new PublicPropertyMapper()));
    }
}
