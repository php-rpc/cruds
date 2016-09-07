<?php

namespace ScayTrase\Api\Cruds\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class EntityCrudEvent extends CrudEvent
{
    /** @var Collection */
    private $entities = [];

    /**
     * EntityCrudEvent constructor.
     *
     * @param array $entities
     */
    public function __construct($entities)
    {
        if ($entities instanceof Collection) {
            $this->entities = $entities;
        } elseif (is_array($entities)) {
            $this->entities = new ArrayCollection($entities);
        } else {
            $this->entities = new ArrayCollection([$entities]);
        }
    }

    /**
     * @return Collection
     */
    public function getEntities()
    {
        return $this->entities;
    }
}
