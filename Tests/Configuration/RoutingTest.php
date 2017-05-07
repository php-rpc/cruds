<?php

namespace ScayTrase\Api\Cruds\Tests\Configuration;

use ScayTrase\Api\Cruds\Tests\AbstractCrudsWebTest;
use ScayTrase\Api\Cruds\Tests\StaticKernelTestTrait;
use Symfony\Component\Routing\RequestContext;

class RoutingTest extends AbstractCrudsWebTest
{
    use StaticKernelTestTrait;

    public function getValidRoutes()
    {
        return [
            'valid create POST' => ['/api/entity/my-entity/create', 'POST'],
            'valid get GET'     => ['/api/entity/my-entity/get', 'GET'],
            'valid get POST'    => ['/api/entity/my-entity/get', 'POST'],
            'valid update POST' => ['/api/entity/my-entity/update', 'POST'],
            'valid delete POST' => ['/api/entity/my-entity/delete', 'POST'],
            'valid search GET'  => ['/api/entity/my-entity/search', 'GET'],
            'valid search POST' => ['/api/entity/my-entity/search', 'POST'],
            'valid count GET'   => ['/api/entity/my-entity/count', 'GET'],
            'valid count POST'  => ['/api/entity/my-entity/count', 'POST'],
        ];
    }

    public function getInvalidRoutes()
    {
        return [
            'invalid create GET'  => ['/api/entity/my-entity/create', 'GET'],
            'invalid update GET'  => ['/api/entity/my-entity/update', 'GET'],
            'invalid delete POST' => ['/api/entity/my-entity/delete', 'GET'],
        ];
    }

    /**
     * @dataProvider getInvalidRoutes
     * @expectedException \Symfony\Component\Routing\Exception\MethodNotAllowedException
     *
     * @param $path
     * @param $method
     */
    public function testPathNotMatches($path, $method)
    {
        $this->createRouter($method)->match($path);
    }

    /**
     * @dataProvider getValidRoutes
     *
     * @param string $path
     * @param string $method
     */
    public function testPathMatches($path, $method)
    {
        self::assertNotNull($this->createRouter($method)->match($path));
    }

    /**
     * @param $method
     *
     * @return object|\Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private function createRouter($method)
    {
        $container = self::$kernel->getContainer();
        $router    = $container->get('router');

        $context = new RequestContext('', $method);
        $router->getMatcher()->setContext($context);

        return $router;
    }
}
