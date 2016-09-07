<?php

namespace ScayTrase\Api\Cruds\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineOrmCompilerPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('doctrine')) {
            return;
        }

        $factory = $container->getDefinition('cruds.factory.reflection');
        $factory->setFactory([new Reference('cruds.factory.doctrine_reflection'), 'create']);

        if ($container->has('cruds.processor.property_access')) {
            $processor = $container->getDefinition('cruds.processor.property_access');
        }
    }
}
