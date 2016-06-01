<?php

namespace ScayTrase\Api\Cruds;

use ScayTrase\Api\Cruds\DependencyInjection\Compiler\JmsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CrudsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new JmsCompilerPass());
    }
}
