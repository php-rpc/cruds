<?php

namespace ScayTrase\Api\Cruds\Adaptors\Jms;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class JmsNormalizerAdapter implements NormalizerInterface
{
    /** @var  Serializer */
    private $serializer;

    /**
     * JmsNormalizerAdapter constructor.
     *
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /** {@inheritdoc} */
    public function normalize($object, $format = null, array $context = [])
    {
        $jmsContext = SerializationContext::create();
        if (array_key_exists('groups', $context)) {
            $jmsContext->setGroups($context['groups']);
        }

        return $this->serializer->toArray($object, $jmsContext);
    }

    /** {@inheritdoc} */
    public function supportsNormalization($data, $format = null)
    {
        return true;
    }
}
