<?php

namespace ScayTrase\Api\Cruds\Adaptors\JmsSerializer;

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
        return $this->serializer->serialize($data, $format, JmsContextFactory::serialization($context));
    }

    /** {@inheritdoc} */
    public function deserialize($data, $type, $format, array $context = [])
    {
        $this->serializer->deserialize($data, $type, $format, JmsContextFactory::deserialization($context));
    }

    /** {@inheritdoc} */
    public function normalize($object, $format = null, array $context = [])
    {
        $jmsContext = JmsContextFactory::serialization($context);

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
