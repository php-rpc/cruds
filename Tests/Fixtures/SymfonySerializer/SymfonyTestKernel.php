<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\SymfonySerializer;

use ScayTrase\Api\Cruds\Tests\CrudsTestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;

class SymfonyTestKernel extends CrudsTestKernel
{
    /** {@inheritdoc} */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        return $loader->load(__DIR__ . '/config.yml');
    }

    /** {@inheritdoc} */
    protected function buildContainer()
    {
        $container = parent::buildContainer();
        $container->addResource(new FileResource(__DIR__ . '/config.yml'));

        return $container;
    }
}
