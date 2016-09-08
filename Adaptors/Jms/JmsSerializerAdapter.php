<?php

namespace ScayTrase\Api\Cruds\Adaptors\Jms;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface as JmsSerializer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class JmsSerializerAdapter implements SerializerInterface, NormalizerInterface
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
        $jmsContext->setSerializeNull(true);

        return $this->serializer->serialize($data, $format, $jmsContext);
    }

    /** {@inheritdoc} */
    public function deserialize($data, $type, $format, array $context = [])
    {
        $jmsContext = DeserializationContext::create();
        if (array_key_exists('groups', $context)) {
            $jmsContext->setGroups($context['groups']);
        }
        $jmsContext->setSerializeNull(true);

        $this->serializer->deserialize($data, $type, $format, $jmsContext);
    }

    /** {@inheritdoc} */
    public function normalize($object, $format = null, array $context = [])
    {
        $jmsContext = SerializationContext::create();
        if (array_key_exists('groups', $context)) {
            $jmsContext->setGroups($context['groups']);
        }
        $jmsContext->setSerializeNull(true);

        if ($this->serializer instanceof Serializer) {
            return $this->serializer->toArray($object, $jmsContext);
        }

        return json_decode($this->serializer->serialize($object, 'json', $jmsContext), true);
    }

    /** {@inheritdoc} */
    public function supportsNormalization($data, $format = null)
    {
        return true;
    }
}
