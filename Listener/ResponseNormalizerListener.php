<?php

namespace ScayTrase\Api\Cruds\Listener;

use Doctrine\Common\Collections\Collection;
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

        if ($entities instanceof Collection) {
            $entities = $entities->toArray();
        }

        $isArray = true;
        if (!is_array($entities)) {
            $isArray  = false;
            $entities = [$entities];
        }

        $route   = $this->getRoute($event);
        $context = [];
        if ($route->hasOption('context')) {
            $context = (array)$route->getOption('context');
        }

        foreach ($entities as &$entity) {
            $entity = $this->normalizer->normalize($entity, null, $context);
        }
        unset($entity);

        $entities = $isArray ? $entities : array_shift($entities);

        $event->setControllerResult($entities);
    }

    /** {@inheritdoc} */
    protected function getRouter()
    {
        return $this->router;
    }
}
