<?php

namespace ScayTrase\Api\Cruds\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class RoleBasedVoter extends EntityVoter
{
    /** @var string[] */
    private $roles = [];
    /** @var string */
    private $attribute;

    /**
     * RoleBasedVoter constructor.
     *
     * @param string   $className
     * @param string   $attribute
     * @param string[] $roles
     */
    public function __construct($className, $attribute, array $roles)
    {
        parent::__construct($className);
        $this->roles     = $roles;
        $this->attribute = $attribute;
    }

    /** {@inheritdoc} */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return count(array_intersect($user->getRoles(), $this->roles)) !== 0;
    }

    /** @return string[] */
    protected function getSupportedAttributes()
    {
        return [$this->attribute];
    }
}
