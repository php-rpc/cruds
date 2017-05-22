<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\SymfonySerializer;

use ScayTrase\Api\Cruds\Tests\Fixtures\Common\CrudsTestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;

final class SymfonyTestKernel extends CrudsTestKernel
{
    /** {@inheritdoc} */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__.'/config.yml');
    }

    /** {@inheritdoc} */
    protected function buildContainer()
    {
        $container = parent::buildContainer();
        $container->addResource(new FileResource(__DIR__.'/config.yml'));

        return $container;
    }
}
