<?php

namespace ScayTrase\Api\Cruds\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;

final class CrudsParamConverter
{
    use CrudsRequestCheckerTrait;

    const PARAMETER_WHITE_LIST = [
        'identifier',
        'data',
        'criteria',
        'order',
        'limit',
        'offset',
    ];

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

        $request = $event->getRequest();
        foreach (self::PARAMETER_WHITE_LIST as $param) {
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
