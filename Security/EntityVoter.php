<?php

namespace ScayTrase\Api\Cruds\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class EntityVoter extends Voter
{
    /** @var string FQCN class */
    private $className;

    /**
     * AnonymousReadVoter constructor.
     *
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }


    /** {@inheritdoc} */
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, $this->getSupportedAttributes(), true) && $subject instanceof $this->className;
    }

    /** @return string[] */
    abstract protected function getSupportedAttributes();
}
