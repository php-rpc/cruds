<?php

namespace ScayTrase\Api\Cruds\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class EntityRouteLoader extends Loader
{
    const RESOURCE_TYPE = 'cruds_mount';
    /** @var  CrudsRoute[][] */
    private $routes = [];
    /** @var bool[] */
    private $loaded = [];

    /** {@inheritdoc} */
    public function load($mount, $type = null)
    {
        $this->assertLoaded($mount);

        $collection = new RouteCollection();
        if (!array_key_exists($mount, $this->routes)) {
            throw new \LogicException(sprintf('No routes configured for %s CRUDS mount point', $mount));
        }

        foreach ($this->routes[$mount] as $name => $route) {
            $collection->add($name, $route);
        }

        $this->loaded = true;

        return $collection;
    }

    /** {@inheritdoc} */
    public function supports($mount, $type = null)
    {
        return self::RESOURCE_TYPE === $type;
    }

    public function addRoute($mount, $name, $path, $controller, array $methods, array $options = [])
    {
        $this->assertLoaded($mount);

        $this->routes[$mount][$name] = CrudsRoute::create($path, $controller, $methods, $options);
    }

    /**
     * @param $resource
     */
    private function assertLoaded($resource)
    {
        if (array_key_exists($resource, $this->loaded) && $this->loaded[$resource]) {
            throw new \LogicException('Already loaded');
        }
    }

    /**
     * @param string $mount
     *
     * @return CrudsRoute[]
     */
    public function getRoutes($mount)
    {
        if (!array_key_exists($mount, $this->routes)){
            throw new \OutOfBoundsException('Mount does not exist');
        }

        return $this->routes[$mount];
    }

    /**
     * @return string[]
     */
    public function getMounts()
    {
        return array_keys($this->routes);
    }
}
