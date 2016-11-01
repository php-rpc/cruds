<?php

namespace ScayTrase\Api\Cruds\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ResponseFormatterListener
{
    private static $formatMap = [
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'yaml' => 'application/x-yaml',
        'yml'  => 'application/x-yaml',
    ];

    use CrudsRequestCheckerTrait;

    /** @var  SerializerInterface */
    private $serializer;
    /** @var RouterInterface */
    private $router;

    /**
     * ResponseNormalizerListener constructor.
     *
     * @param SerializerInterface $serializer
     * @param RouterInterface     $router
     */
    public function __construct(SerializerInterface $serializer, RouterInterface $router)
    {
        $this->router     = $router;
        $this->serializer = $serializer;
    }

    public function filterResponse(GetResponseForControllerResultEvent $event)
    {
        if (!$this->checkRequest($event)) {
            return;
        }

        $request  = $event->getRequest();
        $response = $event->getControllerResult();

        $format = $request->get('_format', 'json');

        $content     = $this->serializer->serialize($response, $format);
        $contentType = array_key_exists($format, self::$formatMap) ? self::$formatMap[$format] : 'text/plain';
        $event->setResponse(
            new Response(
                $content,
                Response::HTTP_OK,
                [
                    'Content-Type' => $contentType,
                ]
            )
        );
    }

    /** {@inheritdoc} */
    protected function getRouter()
    {
        return $this->router;
    }
}
