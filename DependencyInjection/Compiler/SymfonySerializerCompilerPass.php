<?php

namespace ScayTrase\Api\Cruds\DependencyInjection\Compiler;

use ScayTrase\Api\Cruds\Adaptors\DoctrineOrm\CircularReferenceHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
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

        $handler = new Definition(CircularReferenceHandler::class);
        $handler->setArguments([new Reference('doctrine')]);

        $normalizer = new DefinitionDecorator('serializer.normalizer.object');
        $normalizer->addTag('serializer.normalizer', ['priority' => -100]);
        $normalizer->addMethodCall('setCircularReferenceHandler', [[$handler, 'handle']]);

        $container->setDefinition('cruds.object_normalizer', $normalizer);
    }
}
