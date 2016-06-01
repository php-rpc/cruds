<?php

namespace ScayTrase\Api\Cruds\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

final class CrudsExtension extends Extension
{
    /** {@inheritdoc} */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('cruds.yml');

        $this->registerSymfonyFormsCompatibility($container);
        $this->registerJmsSerializerCompatibility($container);
        $this->registerSymfonySerializerCompatibility($container);

        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $configurator = new CrudsEntitiesConfigurator($container);
        $container->addObjectResource($configurator);
        $prefix = $config['prefix'];
        foreach ($config['entities'] as $name => $entityConfig) {
            $entityConfig['prefix'] = $entityConfig['prefix'] ?: '/'.$name;
            $entityConfig['prefix'] = $prefix.$entityConfig['prefix'];
            $configurator->processEntityConfiguration($name, $entityConfig);
        }
    }

    private function registerSymfonyFormsCompatibility(ContainerBuilder $container)
    {
        if (!$container->has('form.registry')) {
            return;
        }
    }

    private function registerJmsSerializerCompatibility(ContainerBuilder $container)
    {
    }

    private function registerSymfonySerializerCompatibility(ContainerBuilder $container)
    {
        if (!$container->has('serializer')) {
            return;
        }
    }
}
