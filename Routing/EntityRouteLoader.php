<?php

namespace ScayTrase\Api\Cruds\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class EntityRouteLoader extends Loader
{
    /** @var  Route[] */
    private $routes = [];

    private $loaded = false;

    /** {@inheritdoc} */
    public function load($resource, $type = null)
    {
        if ($this->loaded) {
            throw new \LogicException('Already loaded');
        }

        $collection = new RouteCollection();
        foreach ($this->routes as $name => $route) {
            $collection->add($name, $route);
        }

        $this->loaded = true;

        return $collection;
    }

    /** {@inheritdoc} */
    public function supports($resource, $type = null)
    {
        return 'cruds' === $type;
    }

    public function addRoute($name, $path, $controller, array $methods)
    {
        if ($this->loaded) {
            throw new \LogicException('Already loaded');
        }

        $this->routes[$name] = new Route(
            $path,
            [
                '_controller' => $controller,
            ],
            [],
            [
                'cruds_api' => true,
            ],
            '',
            [],
            $methods,
            ''
        );
    }
}
