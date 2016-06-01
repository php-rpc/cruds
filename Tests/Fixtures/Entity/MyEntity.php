<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

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
     * @Serializer\SerializedName("public_string_field")
     * @ORM\Column(type="string")
     */
    private $publicApiField;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Serializer\Exclude()
     */
    private $privateField;

    /**
     * MyEntity constructor.
     *
     * @param string $privateField
     */
    public function __construct($privateField = 'test')
    {
        $this->privateField = $privateField;
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
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
