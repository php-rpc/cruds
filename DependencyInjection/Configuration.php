<?php

namespace ScayTrase\Api\Cruds\DependencyInjection;

use ScayTrase\Api\Cruds\EntityProcessorInterface;
use ScayTrase\Api\Cruds\EntityFactoryInterface;
use ScayTrase\Api\Cruds\PropertyAccessProcessor;
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
        $parent->canBeDisabled();

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
            ->scalarNode('mount')
            ->defaultValue('api')
            ->cannotBeEmpty()
            ->info(
                'Route mount. You can create different entries ' .
                'with different mounts. You can use this value when loading routes'
            )
            ->example('my-mount-name');

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
            ->info('Action configuration');

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
                'Service ID implementing ' . PHP_EOL .
                EntityFactoryInterface::class . PHP_EOL .
                'Defaults to ' . ReflectionConstructorFactory::class
            );

        $create
            ->children()
            ->variableNode('processor')
            ->defaultNull()
            ->example('@my_entity.factory')
            ->info(
                'Service ID implementing ' . PHP_EOL .
                EntityFactoryInterface::class . PHP_EOL .
                'Defaults to ' . ReflectionConstructorFactory::class
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
        $criteria->info('Criteria modifiers. Array will be treated as nested criteria, allowing configuring several modifiers by key:value');
        $criteria->defaultValue('cruds.criteria.entity');
        $criteria->example('my.criteria.modifier');
        $criteria->cannotBeEmpty();
    }

    private function configureReadAction(ArrayNodeDefinition $parent)
    {
        $read = $parent
            ->children()
            ->arrayNode('read');
        $this->configureActionNode($read, 'get');
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
            ->example('@my_entity.processor')
            ->info(
                'Service ID implementing ' . PHP_EOL .
                EntityProcessorInterface::class . PHP_EOL .
                'Defaults to ' . PropertyAccessProcessor::class
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
            ->info('Action path (will be prefixed with entity prefix)')
            ->defaultValue('/' . $action);
    }
}
