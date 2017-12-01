<?php

namespace ScayTrase\Api\Cruds\Criteria;

use Doctrine\Common\Collections\Criteria;
use ScayTrase\Api\Cruds\CriteriaConfiguratorInterface;
use ScayTrase\Api\Cruds\Exception\CriteriaConfigurationException;
use ScayTrase\Api\Cruds\Exception\MapperException;
use ScayTrase\Api\Cruds\PropertyMapperInterface;
use ScayTrase\Api\Cruds\ReferenceProviderInterface;

final class EntityCriteriaConfigurator implements CriteriaConfiguratorInterface
{
    const ALIAS_SEPARATOR = '.';

    /** @var PropertyMapperInterface */
    private $mapper;
    /** @var ReferenceProviderInterface */
    private $provider;

    /**
     * DoctrineCriteriaConfigurator constructor.
     *
     * @param PropertyMapperInterface    $mapper
     * @param ReferenceProviderInterface $provider
     */
    public function __construct(PropertyMapperInterface $mapper, ReferenceProviderInterface $provider)
    {
        $this->mapper   = $mapper;
        $this->provider = $provider;
    }

    /** {@inheritdoc} */
    public function configure(string $fqcn, Criteria $criteria, $arguments)
    {
        if (null === $arguments) {
            return;
        }

        if (!is_array($arguments)) {
            throw CriteriaConfigurationException::invalidType('array|null', gettype($criteria));
        }

        foreach ((array)$arguments as $apiProperty => $value) {
            try {
                $mappedProperty = $this->mapper->getEntityProperty($fqcn, $apiProperty);

                if (null === $mappedProperty) {
                    throw CriteriaConfigurationException::invalidData($apiProperty);
                }

                $value = $this->provider->getEntityReference($fqcn, $mappedProperty, $value);
                $this->filterDoctrineProperty($criteria, $mappedProperty, $value);
                unset($arguments[$apiProperty]);
            } catch (MapperException $e) {
                throw CriteriaConfigurationException::invalidProperty($apiProperty, $e);
            }
        }
    }

    /**
     * @param Criteria $criteria
     * @param string   $property
     * @param mixed    $value
     *
     * @throws CriteriaConfigurationException
     */
    private function filterDoctrineProperty(Criteria $criteria, $property, $value)
    {
        switch (true) {
            case is_array($value):
                $criteria->andWhere(Criteria::expr()->in($property, $value));
                break;
            case null === $value:
                $criteria->andWhere(Criteria::expr()->isNull($property));
                break;
            case !is_scalar($value) && !is_object($value):
                throw CriteriaConfigurationException::invalidPropertyType(
                    $property,
                    'scalar|array|null|identifier',
                    gettype($value)
                );
            default:
                $criteria->andWhere(Criteria::expr()->eq($property, $value));
        }
    }
}
