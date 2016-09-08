<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\Common;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use ScayTrase\Api\Cruds\CrudsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
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
        ];
    }

    public function getCacheDir()
    {
        return __DIR__ . '/../../../build/' . $this->getClassName() . '/cache';
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

    /**
     * @return array
     */
    private function getClassName()
    {
        $path = explode('\\', static::class);

        return array_pop($path);
    }
}
