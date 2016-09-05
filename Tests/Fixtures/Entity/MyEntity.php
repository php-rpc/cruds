<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity()
 */
class MyEntity
{
    /**
     * @var int|null
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $publicApiField;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $privateField;

    /**
     * @var MyEntity
     * @ORM\ManyToOne(targetEntity="MyEntity", inversedBy="children")
     */
    private $parent;
    /**
     * @var ArrayCollection|MyEntity[]
     * @ORM\OneToMany(targetEntity="MyEntity", mappedBy="parent", orphanRemoval=false)
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
        $this->parent       = $this;
        $this->children->add($this);
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
        $this->parent = $parent;
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
}
