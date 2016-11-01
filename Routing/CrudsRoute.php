<?php

namespace ScayTrase\Api\Cruds\Routing;

use Symfony\Component\Routing\Route;

final class CrudsRoute extends Route
{
    public static function create($path, $controller, array $methods, array $options = [])
    {
        return new static(
            $path,
            [
                '_controller' => $controller,
            ],
            [],
            [
                'cruds_api' => true,
                'cruds_options' => $options
            ],
            '',
            [],
            $methods,
            ''
        );
    }
}
