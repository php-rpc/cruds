<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\SymfonySerializer;

use ScayTrase\Api\Cruds\Tests\Fixtures\Common\CrudsTestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\HttpKernel\Kernel;

class SymfonyTestKernel extends CrudsTestKernel
{
    /** {@inheritdoc} */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        parent::registerContainerConfiguration($loader);

        if ((int)Kernel::MAJOR_VERSION > 2) {
            $loader->load(__DIR__.'/config_3.0.yml');
        }

        return $loader->load(__DIR__.'/config.yml');
    }

    /** {@inheritdoc} */
    protected function buildContainer()
    {
        $container = parent::buildContainer();
        $container->addResource(new FileResource(__DIR__.'/config.yml'));

        return $container;
    }
}
