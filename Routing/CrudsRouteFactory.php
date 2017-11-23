<?php

namespace ScayTrase\Api\Cruds\Routing;

use Symfony\Component\Routing\Route;

final class CrudsRouteFactory
{
    public static function create($path, $controller, array $methods, array $options = []): Route
    {
        return new Route(
            $path,
            [
                '_controller' => $controller,
                '_cruds_api'  => array_replace(['enabled' => true], $options),
            ],
            [],
            [],
            '',
            [],
            $methods,
            ''
        );
    }
}
