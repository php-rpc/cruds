<?php

namespace ScayTrase\Api\Cruds;

use ScayTrase\Api\Cruds\DependencyInjection\Compiler\DoctrineOrmCompilerPass;
use ScayTrase\Api\Cruds\DependencyInjection\Compiler\JmsSerializerCompilerPass;
use ScayTrase\Api\Cruds\DependencyInjection\Compiler\JmsSerializerDriverPass;
use ScayTrase\Api\Cruds\DependencyInjection\Compiler\SymfonySerializerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CrudsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SymfonySerializerCompilerPass());
        $container->addCompilerPass(new DoctrineOrmCompilerPass());
        $container->addCompilerPass(new JmsSerializerCompilerPass());
        $container->addCompilerPass(new JmsSerializerDriverPass());
    }
}
