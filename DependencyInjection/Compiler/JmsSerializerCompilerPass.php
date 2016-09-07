<?php

namespace ScayTrase\Api\Cruds\DependencyInjection\Compiler;

use ScayTrase\Api\Cruds\Adaptors\Jms\JmsValidatorSubscriber;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

final class JmsSerializerCompilerPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('jms_serializer')) {
            return;
        }

        if (!$container->has('debug.stopwatch')) {
            $container->removeDefinition('jms_serializer.stopwatch_subscriber');
        }

        if (!$container->has('translator')) {
            $container->removeDefinition('jms_serializer.form_error_handler');
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('jms.yml');

        if ($container->has('validator')) {
            $definition = $container->register('cruds.api.jms_serializer.validator', JmsValidatorSubscriber::class);
            $definition->setArguments([new Reference('validator')]);
            $definition->addTag('jms_serializer.event_subscriber');
        }

        $normalizer = $container->getDefinition('cruds.api.response_normalizer_listener');
        $normalizer->replaceArgument(0, new Reference('cruds.jms_serializer'));

        $serializer = $container->getDefinition('cruds.api.response_serializer_listener');
        $serializer->replaceArgument(0, new Reference('cruds.jms_serializer'));
    }
}
