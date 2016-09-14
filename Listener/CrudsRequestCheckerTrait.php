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
        $route = $this->getRoute($event);

        if (null === $route) {
            return false;
        }

        if (!$route->getOption('cruds_api')) {
            return false;
        }

        return true;
    }

    protected function getRoute(KernelEvent $event)
    {
        $request = $event->getRequest();
        $route   = $request->attributes->get('_route');

        if (null === $route) {
            return null;
        }

        return $this->getRouter()->getRouteCollection()->get($route);
    }
}
