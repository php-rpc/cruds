<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\SymfonySerializer;

use ScayTrase\Api\Cruds\Tests\Fixtures\Common\CrudsTestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;

class SymfonyTestKernel extends CrudsTestKernel
{
    /** {@inheritdoc} */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        parent::registerContainerConfiguration($loader);

        if (self::MAJOR_VERSION === '3' || (self::MAJOR_VERSION === '2' && self::MINOR_VERSION === '8')) {
            $loader->load(__DIR__.'/config_2.8.yml');
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
