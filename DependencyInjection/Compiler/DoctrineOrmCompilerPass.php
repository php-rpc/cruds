<?php

namespace ScayTrase\Api\Cruds\DependencyInjection\Compiler;

use ScayTrase\Api\Cruds\Adaptors\DoctrineOrm\CircularReferenceHandler;
use ScayTrase\Api\Cruds\Adaptors\DoctrineOrm\DoctrineObjectNormalizer;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineOrmCompilerPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('doctrine')) {
            return;
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
        $loader->load('doctrine.yml');

        $factory = $container->getDefinition('cruds.factory.reflection');
        $factory->setFactory([new Reference('cruds.factory.doctrine_reflection_factory'), 'create']);

        if ($container->has('serializer.normalizer.object')) {

            $handler = new Definition(CircularReferenceHandler::class);
            $handler->setArguments([new Reference('doctrine')]);

            $container->getDefinition('serializer.normalizer.object')
                      ->addMethodCall('setCircularReferenceHandler', [[$handler, 'handle']]);

            $normalizer = new DefinitionDecorator('serializer.normalizer.object');
            $normalizer->setClass(DoctrineObjectNormalizer::class);
            $normalizer->addMethodCall('setRegistry', [new Reference('doctrine')]);
            $normalizer->addTag('serializer.normalizer', ['priority' => -800]);

            $container->setDefinition('cruds.serializer.doctrine_object_normalizer', $normalizer);
        }
    }
}
