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

        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);

        $configurator = new CrudsEntitiesConfigurator($container);
        $container->addObjectResource($configurator);
        foreach ($config['entities'] as $name => $entityConfig) {
            $entityConfig['prefix'] = $entityConfig['prefix'] ?: '/'.$name;
            $configurator->processEntityConfiguration($name, $entityConfig);
        }
    }
}
