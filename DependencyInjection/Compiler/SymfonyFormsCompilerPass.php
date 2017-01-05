<?php

namespace ScayTrase\Api\Cruds\DependencyInjection\Compiler;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;

final class SymfonyFormsCompilerPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('form.factory')) {
            return;
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
        $loader->load('forms.yml');
    }
}
