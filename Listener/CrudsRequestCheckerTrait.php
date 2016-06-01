<?php

namespace ScayTrase\Api\Cruds\Listener;

use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\Routing\RouterInterface;

trait CrudsRequestCheckerTrait
{
    /** @return RouterInterface */
    abstract protected function getRouter();

    /**
     * @param KernelEvent $event
     *
     * @return bool
     */
    protected function checkRequest(KernelEvent $event)
    {
        $request = $event->getRequest();
        $route   = $request->attributes->get('_route');

        if (null === $route) {
            return false;
        }

        $collection = $this->getRouter()->getRouteCollection();
        $route      = $collection->get($route);

        if (null === $route) {
            return false;
        }

        if (!$route->getOption('cruds_api')) {
            return false;
        }

        return true;
    }
}
