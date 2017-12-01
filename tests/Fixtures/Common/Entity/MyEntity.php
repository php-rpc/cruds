<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\Common\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class MyEntity
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string
     */
    private $publicApiField = 'defaults';

    /**
     * @var string
     */
    private $privateField;

    /**
     * @var MyEntity
     */
    private $parent;
    /**
     * @var ArrayCollection|MyEntity[]
     */
    private $children;

    /**
     * MyEntity constructor.
     *
     * @param string $privateField
     */
    public function __construct($privateField = 'test')
    {
        $this->privateField = $privateField;
        $this->children     = new ArrayCollection();
    }

    /**
     * @return MyEntity
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param MyEntity $parent
     */
    public function setParent(MyEntity $parent = null)
    {
        if (null !== $this->parent) {
            $this->parent->removeChild($this);
        }

        $this->parent = $parent;

        if (null !== $this->parent) {
            $this->parent->addChild($this);
        }
    }

    /**
     * @return string
     */
    public function getPublicApiField()
    {
        return $this->publicApiField;
    }

    /**
     * @param string $publicApiField
     */
    public function setPublicApiField($publicApiField)
    {
        $this->publicApiField = $publicApiField;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return MyEntity[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function addChild(MyEntity $entity)
    {
        if ($this->children->contains($entity)) {
            $this->children->removeElement($entity);
        }
    }

    public function removeChild(MyEntity $entity)
    {
        if (!$this->children->contains($entity)) {
            $this->children->add($entity);
        }
    }
}
