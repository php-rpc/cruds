<?php

namespace ScayTrase\Api\Cruds\Exception;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CrudAccessException extends AccessDeniedException implements CrudsExceptionInterface
{
    public static function denied($permission)
    {
        return new static(sprintf('No "%s" access', $permission));
    }
}
