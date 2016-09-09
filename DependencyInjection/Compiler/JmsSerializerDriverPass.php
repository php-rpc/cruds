<?php

namespace ScayTrase\Api\Cruds\DependencyInjection\Compiler;

use ScayTrase\Api\Cruds\Adaptors\JmsSerializer\JmsDoctrineHandler;
use ScayTrase\Api\Cruds\Adaptors\JmsSerializer\JmsDoctrineMetadataDriver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class JmsSerializerDriverPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('jms_serializer.metadata.doctrine_type_driver')) {
            return;
        }

        $factory = $container->getDefinition('jms_serializer.metadata_factory');

        $driver = $factory->getArgument(0);

        $definition = $container
            ->register('cruds.jms.doctrine_metadata_driver', JmsDoctrineMetadataDriver::class)
            ->setArguments([
                $driver,
                new Reference('doctrine'),
            ]);

        $factory->replaceArgument(0, $definition);

        $handler = $container->register('cruds.jms.doctrine_associations_handler', JmsDoctrineHandler::class);
        $handler->addArgument(new Reference('doctrine'));

        $formats    = ['json', 'xml', 'array'];
        $directions = [
            'serialization'   => 'serializeRelation',
            'deserialization' => 'deserializeRelation',
        ];
        $type       = JmsDoctrineHandler::TYPE;
        $name       = 'jms_serializer.handler';

        foreach ($formats as $format) {
            foreach ($directions as $direction => $method) {
                $handler->addTag(
                    $name,
                    [
                        'type'      => $type,
                        'direction' => $direction,
                        'format'    => $format,
                        'method'    => $method,
                    ]
                );
            }
        }
    }
}
