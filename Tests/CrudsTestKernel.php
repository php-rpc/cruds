<?php

namespace ScayTrase\Api\Cruds\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use ScayTrase\Api\Cruds\CrudsBundle;
use ScayTrase\Api\Cruds\Tests\Fixtures\Common\MyBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
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
        return __DIR__ . '/../build/' . $this->getClassName() . '/cache';
    }

    public function getLogDir()
    {
        return __DIR__ . '/../build/' . $this->getClassName() . '/logs';
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
