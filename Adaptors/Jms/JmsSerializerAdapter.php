<?php

namespace ScayTrase\Api\Cruds\Adaptors\Jms;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface as JmsSerializer;
use Symfony\Component\Serializer\SerializerInterface;

final class JmsSerializerAdapter implements SerializerInterface
{
    /** @var  JmsSerializer */
    private $serializer;

    /**
     * JmsSerializerAdapter constructor.
     *
     * @param JmsSerializer $serializer
     */
    public function __construct(JmsSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /** {@inheritdoc} */
    public function serialize($data, $format, array $context = [])
    {
        $jmsContext = SerializationContext::create();
        if (array_key_exists('groups', $context)) {
            $jmsContext->setGroups($context['groups']);
        }

        return $this->serializer->serialize($data, $format, $jmsContext);
    }

    /** {@inheritdoc} */
    public function deserialize($data, $type, $format, array $context = [])
    {
        $jmsContext = DeserializationContext::create();
        if (array_key_exists('groups', $context)) {
            $jmsContext->setGroups($context['groups']);
        }

        $this->serializer->deserialize($data, $type, $format, $jmsContext);
    }
}
