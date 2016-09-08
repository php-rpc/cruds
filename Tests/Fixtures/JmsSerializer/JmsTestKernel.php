<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\JmsSerializer;

use JMS\SerializerBundle\JMSSerializerBundle;
use ScayTrase\Api\Cruds\Tests\Fixtures\Common\CrudsTestKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;

class JmsTestKernel extends CrudsTestKernel
{
    /** {@inheritdoc} */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        parent::registerContainerConfiguration($loader);

        return $loader->load(__DIR__ . '/config.yml');
    }

    /** {@inheritdoc} */
    public function registerBundles()
    {
        return array_merge(
            parent::registerBundles(),
            [
                new JMSSerializerBundle(),
            ]
        );
    }

    /** {@inheritdoc} */
    protected function buildContainer()
    {
        $container = parent::buildContainer();
        $container->addResource(new FileResource(__DIR__ . '/config.yml'));

        return $container;
    }
}
