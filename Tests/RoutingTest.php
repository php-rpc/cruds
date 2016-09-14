<?php

namespace ScayTrase\Api\Cruds\Tests;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

class RoutingTest extends AbstractCrudsWebTest
{

    /**
     * @dataProvider getKernelClasses
     *
     * @param $kernel
     */
    public function testEntityRouting($kernel)
    {
        self::createAndBootKernel($kernel);

        $this->matchPath('/api/entity/my-entity/create', 'POST');
        $this->matchPath('/api/entity/my-entity/create', 'GET', true);
        $this->matchPath('/api/entity/my-entity/get', 'GET');
        $this->matchPath('/api/entity/my-entity/get', 'POST', true);
        $this->matchPath('/api/entity/my-entity/update', 'POST');
        $this->matchPath('/api/entity/my-entity/update', 'GET', true);
        $this->matchPath('/api/entity/my-entity/delete', 'POST');
        $this->matchPath('/api/entity/my-entity/delete', 'GET', true);
        $this->matchPath('/api/entity/my-entity/search', 'GET');
        $this->matchPath('/api/entity/my-entity/search', 'POST');
    }

    private function matchPath($path, $method = 'GET', $catch = false)
    {
        $container = self::$kernel->getContainer();
        $router    = $container->get('router');

        $context = new RequestContext('', $method);
        $router->getMatcher()->setContext($context);

        try {
            $router->match($path);
        } catch (MethodNotAllowedException $exception) {
            if (!$catch) {
                self::fail(
                    sprintf(
                        'Method %s not allowed. Allowed methods are: %s',
                        $context->getMethod(),
                        implode(', ', $exception->getAllowedMethods())
                    )
                );
            }
        } catch (ResourceNotFoundException $exception) {
            if (!$catch) {
                self::fail(sprintf('Resource not found: %s', $path));
            }
        }
    }
}
