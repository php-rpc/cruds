<?php

namespace ScayTrase\Api\Cruds\Security;

use ScayTrase\Api\Cruds\Crud;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class AnonymousReadVoter extends EntityVoter
{
    protected function getSupportedAttributes()
    {
        return [Crud::PERMISSION_READ];
    }

    /** {@inheritdoc} */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // Allow read permission to this class disregarding the presence of token
        return true;
    }
}
