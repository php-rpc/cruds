<?php

namespace ScayTrase\Api\Cruds\Listener;

use ScayTrase\Api\Cruds\CrudsBundle;
use Symfony\Component\HttpKernel\Event\KernelEvent;

trait CrudsRequestCheckerTrait
{
    /**
     * @param KernelEvent $event
     *
     * @return bool
     */
    protected function checkRequest(KernelEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->get(CrudsBundle::CRUDS_REQUEST_ATTRIBUTE)) {
            return false;
        }

        return true;
    }

    protected function getNormalizedCrudApiOptions(KernelEvent $event)
    {
        $options = $event->getRequest()->attributes->get(CrudsBundle::CRUDS_REQUEST_ATTRIBUTE);
        if (!is_array($options)) {
            return [
                'enabled'   => (bool)$options,
                'arguments' => [],
                'context'   => [],
            ];
        }

        return array_merge(['enabled' => false, 'arguments' => [], 'context' => []], $options);
    }
}
