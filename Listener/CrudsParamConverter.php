<?php

namespace ScayTrase\Api\Cruds\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;

final class CrudsParamConverter
{
    use CrudsRequestCheckerTrait;

    /** @var  RouterInterface */
    private $router;

    /**
     * CrudsParamConverter constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onCrudRequest(GetResponseEvent $event)
    {
        if (!$this->checkRequest($event)) {
            return;
        }

        $route = $this->getRoute($event);
        if (!$route->hasOption('cruds_options')) {
            return;
        }

        $options = (array)$route->getOption('cruds_options');
        if (!array_key_exists('arguments', $options)) {
            return;
        }

        $request = $event->getRequest();
        foreach ((array)$options['arguments'] as $param) {
            $value = $request->get($param);
            if (null !== $value) {
                $request->attributes->set($param, $value);
            }
        }
    }

    /** {@inheritdoc} */
    protected function getRouter()
    {
        return $this->router;
    }
}
