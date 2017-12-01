<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\Common\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class ReferenceEntity
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var MyEntity
     */
    private $reference;

    /**
     * @var ReferenceEntity
     */
    private $parent;

    /**
     * @var ReferenceEntity[]|ArrayCollection
     */
    private $children;

    /**
     * ReferenceEntity constructor.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return MyEntity
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param MyEntity $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return ReferenceEntity
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param ReferenceEntity $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return ReferenceEntity[]
     */
    public function getChildren()
    {
        return $this->children->toArray();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }
}
