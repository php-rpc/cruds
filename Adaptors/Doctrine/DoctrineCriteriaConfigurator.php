<?php

namespace ScayTrase\Api\Cruds\Adaptors\Doctrine;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use ScayTrase\Api\Cruds\CriteriaConfiguratorInterface;
use ScayTrase\Api\Cruds\Exception\FilterException;
use ScayTrase\Api\Cruds\Exception\MapperException;
use ScayTrase\Api\Cruds\PropertyMapperInterface;

final class DoctrineCriteriaConfigurator implements CriteriaConfiguratorInterface
{
    const ALIAS_SEPARATOR = '.';
    /** @var  PropertyMapperInterface */
    private $mapper;

    /**
     * DoctrineCriteriaConfigurator constructor.
     *
     * @param PropertyMapperInterface $mapper
     */
    public function __construct(PropertyMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /** {@inheritdoc} */
    public function configure(QueryBuilder $builder, $criteria)
    {
        if (null === $criteria) {
            return;
        }

        if (!is_array($criteria)) {
            throw FilterException::invalidType('array', gettype($criteria));
        }

        $aliases   = $builder->getRootAliases();
        $entities  = $this->getRootEntities($builder);
        $rootAlias = $aliases[0];

        try {
            foreach ((array)$criteria as $apiProperty => $value) {
                $alias = $rootAlias;
                if (false !== strpos($apiProperty, self::ALIAS_SEPARATOR)) {
                    list($alias, $apiProperty) = explode(self::ALIAS_SEPARATOR, $apiProperty, 2);
                    if ('' === $alias) {
                        $alias = $rootAlias;
                    }
                }

                if (!in_array($alias, $aliases, true)) {
                    throw FilterException::unknown($apiProperty);
                }

                $entity         = $entities[$alias];
                $mappedProperty = $this->mapper->getObjectProperty($entity, $apiProperty);

                if (null !== $mappedProperty) {
                    $entityProperty = $alias.'.'.$mappedProperty;
                    $this->filterDoctrineProperty($builder, $entityProperty, $value);
                    unset($criteria[$apiProperty]);
                }
            }
        } catch (MapperException $e) {
            throw new FilterException(sprintf('Error getting object property: %s', $e->getMessage()));
        }
    }

    /**
     * @param QueryBuilder $builder
     * @param string       $property
     * @param mixed        $value
     *
     * @throws FilterException
     */
    private function filterDoctrineProperty(QueryBuilder $builder, $property, $value)
    {
        switch (true) {
            case is_array($value):
                $builder->andWhere($builder->expr()->in($property, $value));
                break;
            case null === $value:
                $builder->andWhere($builder->expr()->isNull($property));
                break;
            case !is_scalar($value):
                throw FilterException::invalid($property, $value);
            default:
                $builder->andWhere($builder->expr()->eq($property, $value));
        }
    }

    /**
     * @param QueryBuilder $builder
     *
     * @return string[]
     */
    private function getRootEntities(QueryBuilder $builder)
    {
        $entities = [];

        foreach ((array)$builder->getDQLPart('from') as $fromClause) {
            if (is_string($fromClause)) {
                $spacePos = strrpos($fromClause, ' ');
                $from     = substr($fromClause, 0, $spacePos);
                $alias    = substr($fromClause, $spacePos + 1);

                $fromClause = new Expr\From($from, $alias);
            }

            $entities[$fromClause->getAlias()] = $fromClause->getFrom();
        }

        return $entities;
    }
}
