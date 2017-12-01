<?php

namespace ScayTrase\Api\Cruds\DependencyInjection\Compiler;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
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
            $class = $container->getParameter('serializer.class');
        }

        if (!in_array(SerializerInterface::class, class_implements($class), true)) {
            return;
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
        $loader->load('symfony_serializer.yml');

        /** @var Reference $converter */
        $objectNormalizer = $container->getDefinition('serializer.normalizer.object');

        if ($this->shouldHaveNormalizerConfigured($container)) {
            $converter = $objectNormalizer->getArgument(1);
        } elseif ($container->has('serializer.name_converter.camel_case_to_snake_case')) {
            $converter = new Reference('serializer.name_converter.camel_case_to_snake_case');
        } else {
            $container->register(
                'serializer.name_converter.camel_case_to_snake_case',
                CamelCaseToSnakeCaseNameConverter::class
            );
            $converter = new Reference('serializer.name_converter.camel_case_to_snake_case');
            $objectNormalizer->setArguments(
                array_replace(
                    $objectNormalizer->getArguments(),
                    [
                        1 => $converter,
                    ]
                )
            );
        }

        $container->setAlias('serializer.normalizer.object.name_converter', (string)$converter);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return bool
     */
    private function shouldHaveNormalizerConfigured(ContainerBuilder $container)
    {
        $kernel = $container->getDefinition('kernel')->getClass();

        return $kernel instanceof Kernel &&
               (
                   $kernel::MAJOR_VERSION === '3' ||
                   (
                       $kernel::MAJOR_VERSION === '2'
                       && $kernel::MINOR_VERSION === '8'
                   )
               );
    }
}
