<?php

namespace ScayTrase\Api\Cruds\DependencyInjection\Compiler;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Serializer\SerializerInterface;

final class SymfonySerializerCompilerPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('serializer') || $container->hasAlias('serializer')) {

            return;
        }

        $class = $container->getDefinition('serializer')->getClass();

        if ('%serializer.class%' === $class) {
            // 2.x Definition
            $class = $container->getParameter($class);
        }

        if (!in_array(SerializerInterface::class, class_implements($class), true)) {
            return;
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
        $loader->load('symfony_serializer.yml');

        /** @var Reference $converter */
        $converter = $container->getDefinition('serializer.normalizer.object')->getArgument(1);

        $container->setAlias('serializer.normalizer.object.name_converter', (string)$converter);
    }
}
