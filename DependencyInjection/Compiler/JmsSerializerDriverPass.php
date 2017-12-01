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

        $handler = $container->register('cruds.jms.doctrine_associations_handler', JmsDoctrineHandler::class);

        //fixme: JMS utilizes only public handlers for lazyness
        $handler->setPublic(true);
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
