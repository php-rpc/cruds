<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\Common\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ReferenceEntity
{
    /**
     * @var int|null
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var MyEntity
     * @ORM\ManyToOne(targetEntity="MyEntity")
     */
    private $reference;

    /**
     * @var ReferenceEntity
     * @ORM\ManyToOne(targetEntity="ReferenceEntity", inversedBy="children")
     */
    private $parent;

    /**
     * @var ReferenceEntity[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="ReferenceEntity", mappedBy="parent")
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
     * @return ArrayCollection|ReferenceEntity[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection|ReferenceEntity[] $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }
}
