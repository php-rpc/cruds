<?php

namespace ScayTrase\Api\Cruds\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class EntityRouteLoader extends Loader
{
    const RESOURCE_TYPE = 'cruds_mount';
    /** @var  Route[][] */
    private $routes = [];
    /** @var bool[] */
    private $loaded = [];

    /** {@inheritdoc} */
    public function load($mount, $type = null)
    {
        $this->assertLoaded($mount);

        $collection = new RouteCollection();
        if (!array_key_exists($mount, $this->routes)) {
            return $collection;
        }

        foreach ($this->routes[$mount] as $name => $route) {
            $collection->add($name, $route);
        }

        $this->loaded[$mount] = true;

        return $collection;
    }

    /** {@inheritdoc} */
    public function supports($mount, $type = null): bool
    {
        return self::RESOURCE_TYPE === $type;
    }

    /**
     * @param string $mount
     * @param string $name
     * @param string $path
     * @param string $controller
     * @param array $methods
     * @param array $options
     *
     * @throws \LogicException
     */
    public function addRoute($mount, $name, $path, $controller, array $methods, array $options = [])
    {
        $this->assertLoaded($mount);

        $this->routes[$mount][$name] = CrudsRouteFactory::create($path, $controller, $methods, $options);
    }

    /**
     * @param string $mount
     *
     * @return Route[]
     *
     * @throws \OutOfBoundsException if mount does not exist
     */
    public function getRoutes($mount): array
    {
        if (!array_key_exists($mount, $this->routes)) {
            throw new \OutOfBoundsException('Mount does not exist');
        }

        return $this->routes[$mount];
    }

    /**
     * @return string[]
     */
    public function getMounts(): array
    {
        return array_keys($this->routes);
    }

    /**
     * @param $resource
     *
     * @throws \LogicException
     */
    private function assertLoaded($resource)
    {
        if (array_key_exists($resource, $this->loaded) && $this->loaded[$resource]) {
            throw new \LogicException('Already loaded');
        }
    }
}
