<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\Common;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Liip\FunctionalTestBundle\LiipFunctionalTestBundle;
use ScayTrase\Api\Cruds\CrudsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\HttpKernel\Kernel;

abstract class CrudsTestKernel extends Kernel
{
    /** {@inheritdoc} */
    public function registerBundles()
    {
        return [
            new CrudsBundle(),
            new DoctrineBundle(),
            new MyBundle(),
            new FrameworkBundle(),
            new LiipFunctionalTestBundle(),
        ];
    }

    public function getLogDir()
    {
        return __DIR__ . '/../../../build/' . $this->getClassName() . '/logs';
    }

    /** {@inheritdoc} */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        return $loader->load(__DIR__ . '/config.yml');
    }

    public function getCacheDir()
    {
        return __DIR__ . '/../../../build/' . $this->getClassName() . '/cache';
    }

    protected function initializeContainer()
    {
        $class = $this->getContainerClass();
        $cache = new ConfigCache($this->getCacheDir() . '/' . $class . '.php', $this->debug);

        $container = $this->buildContainer();
        $container->compile();
        $this->dumpContainer($cache, $container, $class, $this->getContainerBaseClass());

        parent::initializeContainer();
    }

    /** {@inheritdoc} */
    protected function buildContainer()
    {
        $container = parent::buildContainer();
        $container->addResource(new FileResource(__DIR__ . '/config.yml'));
        $container->addResource(new FileResource(__DIR__ . '/routing.yml'));

        return $container;
    }

    /**
     * @return array
     */
    private function getClassName()
    {
        $path = explode('\\', static::class);

        return array_pop($path);
    }
}
