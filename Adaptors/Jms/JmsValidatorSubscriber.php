<?php

namespace ScayTrase\Api\Cruds\Adaptors\Jms;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use ScayTrase\Api\Cruds\Exception\EntityProcessingException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class JmsValidatorSubscriber implements EventSubscriberInterface
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public static function getSubscribedEvents()
    {
        return [
            ['event' => 'serializer.post_deserialize', 'method' => 'onPostDeserialize'],
        ];
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
        $context = $event->getContext();

        if ($context->getDepth() > 0) {
            return;
        }

        $groups = $context->attributes->get('groups')->getOrElse(null);

        $list = $this->validator->validate($event->getObject(), $groups);

        if ($list->count() > 0) {
            throw new EntityProcessingException(
                'Data for the entity is not valid',
                array_map(
                    function (ConstraintViolationInterface $violation) {
                        return $violation->getMessage();
                    },
                    iterator_to_array($list)
                )
            );
        }
    }
}
