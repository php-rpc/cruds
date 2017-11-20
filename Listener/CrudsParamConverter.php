<?php

namespace ScayTrase\Api\Cruds\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class CrudsParamConverter
{
    use CrudsRequestCheckerTrait;

    public function onCrudRequest(GetResponseEvent $event)
    {
        if (!$this->checkRequest($event)) {
            return;
        }

        $options = $this->getNormalizedCrudApiOptions($event);
        if (!$options['enabled']) {
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
}
