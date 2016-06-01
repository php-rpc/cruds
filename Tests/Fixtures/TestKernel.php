<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use ScayTrase\Api\Cruds\CrudsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    /** {@inheritdoc} */
    public function registerBundles()
    {
        return [
            new CrudsBundle(),
            new JMSSerializerBundle(),
            new DoctrineBundle(),
            new FrameworkBundle(),
            new MyBundle(),
        ];
    }

    /** {@inheritdoc} */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        return $loader->load(__DIR__.'/config.yml');
    }

    public function getCacheDir()
    {
        return __DIR__.'/../../build/cache';
    }

    public function getLogDir()
    {
        return __DIR__.'/../../build/logs';
    }

    protected function buildContainer()
    {
        $container = parent::buildContainer();
        $container->addResource(new FileResource(__DIR__.'/config.yml'));

        return $container;
    }


}
