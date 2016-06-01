<?php

namespace ScayTrase\Api\Cruds\Event;

final class EntityCrudEvent extends CrudEvent
{
    /** @var array */
    private $entities = [];

    /**
     * EntityCrudEvent constructor.
     *
     * @param array $entities
     */
    public function __construct(array $entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param array $entities
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
    }
}
