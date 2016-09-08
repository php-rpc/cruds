<?php

namespace ScayTrase\Api\Cruds\DependencyInjection;

use ScayTrase\Api\Cruds\ObjectFactoryInterface;
use ScayTrase\Api\Cruds\ReflectionConstructorFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /** {@inheritdoc} */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root    = $builder->root('cruds');

        $root
            ->children()
            ->scalarNode('prefix')
            ->defaultNull()
            ->info('Route prefix')
            ->example('/api');

        $entities = $root->children()->arrayNode('entities');
        $entities->useAttributeAsKey('name');
        $entities->normalizeKeys(false);
        /** @var ArrayNodeDefinition $entitiesProto */
        $entitiesProto = $entities->prototype('array');

        $this->configureEntityProto($entitiesProto);

        return $builder;
    }

    private function configureEntityProto(ArrayNodeDefinition $parent)
    {
        $parent
            ->children()
            ->scalarNode('class')
            ->isRequired()
            ->info('Doctrine class')
            ->example('MyBundle:MyEntity');

        $parent
            ->children()
            ->scalarNode('prefix')
            ->defaultNull()
            ->info('Route prefix. Defaults to entity key if not set')
            ->example('/my-entity');

        $parent
            ->children()
            ->scalarNode('repository')
            ->defaultNull()
            ->info('Entity repository. service reference, default to factory-acquired doctrine repository')
            ->example('@my_entity.repository');


        $actions = $parent
            ->children()
            ->arrayNode('actions');

        $actions
            ->beforeNormalization()
            ->ifArray()
            ->then(
                function (array $v) {
                    if (array_keys($v) !== range(0, count($v) - 1)) {
                        return $v;
                    }

                    $result = [];
                    foreach ($v as $key) {
                        $result[$key] = ['enabled' => true];
                    }

                    return $result;
                }
            )
            ->end()
            ->info('Action configuration')
            ->example(
                [
                    'create' => ['enabled' => false],
                    'read'   => null,
                    'update' => null,
                    'delete' => ['enabled' => true, 'path' => '/remove'],
                    'search' => null,
                ]
            );

        $this->configureCreateAction($actions);
        $this->configureReadAction($actions);
        $this->configureUpdateAction($actions);
        $this->configureDeleteAction($actions);
        $this->configureSearchAction($actions);
    }

    private function configureCreateAction(ArrayNodeDefinition $parent)
    {
        $create = $parent
            ->children()
            ->arrayNode('create');

        $create
            ->children()
            ->variableNode('factory')
            ->defaultNull()
            ->example('@my_entity.factory')
            ->info(
                'Service ID implementing '.PHP_EOL.
                ObjectFactoryInterface::class.PHP_EOL.
                'Defaults to '.ReflectionConstructorFactory::class
            );

        $create
            ->children()
            ->variableNode('processor')
            ->defaultNull()
            ->example('@my_entity.factory')
            ->info(
                'Service ID implementing '.PHP_EOL.
                ObjectFactoryInterface::class.PHP_EOL.
                'Defaults to '.ReflectionConstructorFactory::class
            );

        $this->configureActionNode($create, 'create');
    }

    private function configureSearchAction(ArrayNodeDefinition $parent)
    {
        $search = $parent
            ->children()
            ->arrayNode('search');
        $this->configureActionNode($search, 'search');

        $criteria = $search->children()->variableNode('criteria');
        $criteria
            ->defaultValue([])
            ->beforeNormalization()
            ->ifString()
            ->then(
                function ($v) {
                    return [$v];
                }
            )
            ->ifNull()
            ->thenEmptyArray();

        $criteria->info('List of prioritized criteria modifiers');
        $criteria->example(
            [
                '@my.criteria.modifier',
            ]
        );
    }


    private function configureReadAction(ArrayNodeDefinition $parent)
    {
        $read = $parent
            ->children()
            ->arrayNode('read');
        $this->configureActionNode($read, 'read');
    }

    private function configureUpdateAction(ArrayNodeDefinition $parent)
    {
        $update = $parent
            ->children()
            ->arrayNode('update');

        $update
            ->children()
            ->variableNode('processor')
            ->defaultNull()
            ->example('@my_entity.factory')
            ->info(
                'Service ID implementing '.PHP_EOL.
                ObjectFactoryInterface::class.PHP_EOL.
                'Defaults to '.ReflectionConstructorFactory::class
            );

        $this->configureActionNode($update, 'update');
    }

    private function configureDeleteAction(ArrayNodeDefinition $parent)
    {
        $delete = $parent
            ->children()
            ->arrayNode('delete');
        $this->configureActionNode($delete, 'delete');
    }

    private function configureActionNode(ArrayNodeDefinition $parent, $action)
    {
        $parent->canBeEnabled();

        $parent
            ->children()
            ->scalarNode('path')
            ->example('/'.$action)
            ->info('Action path (prefixed)')
            ->defaultValue('/'.$action);
    }
}
