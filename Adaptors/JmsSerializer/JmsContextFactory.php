<?php

namespace ScayTrase\Api\Cruds\Adaptors\JmsSerializer;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;

final class JmsContextFactory
{
    /**
     * @param null|array $context
     *
     * @return SerializationContext
     */
    public static function serialization(array $context = null)
    {
        $jmsContext = SerializationContext::create();

        self::configureContext($jmsContext, $context);

        return $jmsContext;
    }

    public static function deserialization(array $context = null)
    {
        $jmsContext = DeserializationContext::create();

        self::configureContext($jmsContext, $context);

        return $jmsContext;
    }

    private static function configureContext(Context $jmsContext, array $context = null)
    {
        $jmsContext->setSerializeNull(true);

        if (null === $context) {
            return;
        }

        if (array_key_exists('groups', $context)) {
            $jmsContext->setGroups($context['groups']);
        }

        if (array_key_exists('object_to_populate', $context)) {
            $jmsContext->setAttribute('target', $context['object_to_populate']);
        }
    }
}
