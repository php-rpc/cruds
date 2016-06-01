<?php

namespace ScayTrase\Api\Cruds\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ResponseNormalizerListener
{
    use CrudsRequestCheckerTrait;

    /** @var  NormalizerInterface */
    private $normalizer;
    /** @var  RouterInterface */
    private $router;

    /**
     * ResponseNormalizerListener constructor.
     *
     * @param NormalizerInterface $normalizer
     * @param RouterInterface     $router
     */
    public function __construct(NormalizerInterface $normalizer, RouterInterface $router)
    {
        $this->normalizer = $normalizer;
        $this->router     = $router;
    }

    public function filterResponse(GetResponseForControllerResultEvent $event)
    {
        if (!$this->checkRequest($event)) {
            return;
        }

        $entities = $event->getControllerResult();

        if (null === $entities) {
            return null;
        }

        if (!is_array($entities)) {
            $entities = [$entities];
        }

        foreach ($entities as &$entity) {
            $entity = $this->normalizer->normalize($entity);
        }
        unset($entity);

        $event->setControllerResult($entities);
    }

    /** {@inheritdoc} */
    protected function getRouter()
    {
        return $this->router;
    }
}
