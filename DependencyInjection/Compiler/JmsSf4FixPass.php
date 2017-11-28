<?php

namespace ScayTrase\Api\Cruds\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class JmsSf4FixPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        $this->setPublicAlias($container, 'jms_serializer.metadata_driver');
        $this->setPublicDefinition($container, 'jms_serializer.metadata_driver');
        $this->setPublicDefinition($container, 'jms_serializer.doctrine_proxy_subscriber');
        $this->setPublicDefinition($container, 'jms_serializer.array_collection_handler');
    }

    private function setPublicDefinition(ContainerBuilder $builder, $id)
    {
        $builder->has($id) && $builder->findDefinition($id)->setPublic(true);
    }

    private function setPublicAlias(ContainerBuilder $builder, $id)
    {
        $builder->hasAlias($id) && $builder->getAlias($id)->setPublic(true);
    }
}
