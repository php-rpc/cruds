<?php

namespace ScayTrase\Api\Cruds;

use ScayTrase\Api\Cruds\Exception\MapperException;

interface PropertyMapperInterface
{
    /**
     * @param string $className
     * @param string $apiProperty
     *
     * @return null|string
     * @throws MapperException
     */
    public function getEntityProperty($className, $apiProperty);

    /**
     * @param string $className
     * @param string $objectProperty
     *
     * @return null|string
     * @throws MapperException
     */
    public function getApiProperty($className, $objectProperty);

    /**
     * @param string $className
     *
     * @return string[]
     * @throws MapperException
     */
    public function getApiProperties($className): array;

    /**
     * @param string $className
     *
     * @return string[]
     * @throws MapperException
     */
    public function getEntityProperties($className): array;
}
