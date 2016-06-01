<?php

namespace ScayTrase\Api\Cruds\Listener;

use ScayTrase\Api\Cruds\Crud;
use ScayTrase\Api\Cruds\Exception\CrudAccessException;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class AccessCheckerListener
{
    /** @var  AuthorizationCheckerInterface */
    private $checker;

    /**
     * AccessCheckerListener constructor.
     *
     * @param AuthorizationCheckerInterface $checker
     */
    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    public function filterResponse(GetResponseForControllerResultEvent $event)
    {
        $entities = $event->getControllerResult();

        if (!is_array($entities)) {
            $entities = [$entities];
        }

        foreach ($entities as $entity) {
            if (!$this->checker->isGranted(Crud::PERMISSION_READ, $entity)) {
                throw CrudAccessException::denied(Crud::PERMISSION_READ);
            }
        }
    }

    public function checkAction()
    {

    }
}
