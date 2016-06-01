<?php

namespace ScayTrase\Api\Cruds\DependencyInjection;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use ScayTrase\Api\Cruds\Adaptors\Doctrine\DoctrineReflectionFactory;
use ScayTrase\Api\Cruds\Adaptors\Doctrine\RelationAwareProcessorDecorator;
use ScayTrase\Api\Cruds\Adaptors\Symfony\PropertyAccessProcessor;
use ScayTrase\Api\Cruds\Controller\CreateController;
use ScayTrase\Api\Cruds\Controller\DeleteController;
use ScayTrase\Api\Cruds\Controller\ReadController;
use ScayTrase\Api\Cruds\Controller\SearchController;
use ScayTrase\Api\Cruds\Controller\UpdateController;
use ScayTrase\Api\Cruds\Factory\ReflectionConstructorFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class CrudsEntitiesConfigurator
{
    /** @var  ContainerBuilder */
    private $container;

    /**
     * CrudsEntitiesConfigurator constructor.
     *
     * @param ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function processEntityConfiguration($name, $config)
    {
        $class      = $config['class'];
        $actions    = $config['actions'];
        $prefix     = $config['prefix'];
        $repository = $config['repository'];

        if (null === $repository) {
            $repositoryDefinition = new Definition(EntityRepository::class);
            $repositoryDefinition->setFactory([new Reference('doctrine.orm.entity_manager'), 'getRepository']);
            $repositoryDefinition->setArguments([$class]);
        } else {
            $repositoryDefinition = new Reference($this->filterReference($repository));
        }

        //todo: try to use any ObjectManager as a dependency
        $manager = new Definition(ObjectManager::class);
        $manager->setFactory([new Reference('doctrine'), 'getManagerForClass']);
        $manager->setArguments([$class]);

        foreach ($actions as $action => $actionConfig) {
            $actionConfig['name']       = $name;
            $actionConfig['class']      = $class;
            $actionConfig['repository'] = $repositoryDefinition;
            $actionConfig['path']       = $prefix.$actionConfig['path'];
            $actionConfig['manager']    = $manager;
            $function                   = new \ReflectionMethod($this, 'register'.ucfirst($action).'Action');
            $args                       = [];

            foreach ($function->getParameters() as $parameter) {
                if (array_key_exists($parameter->getName(), $actionConfig)) {
                    $args[] = $actionConfig[$parameter->getName()];
                } else {
                    $args[] = $parameter->getDefaultValue();
                }
            }
            $function->invokeArgs($this, $args);
        }
    }

    public function registerCreateAction($name, $class, $factory, $processor, $path, $manager)
    {
        if (null === $factory) {
            $factory = new Definition(ReflectionConstructorFactory::class);
            $factory->setArguments([$class]);

            //todo: try to recognize services before configuration
            if (true || $this->container->has('doctrine')) {
                $factory = new Definition(DoctrineReflectionFactory::class);
                $factory->setArguments(
                    [
                        new Reference('doctrine'),
                        $class,
                        [],
                    ]
                );
            }
        } else {
            $factory = new Reference($this->filterReference($factory));
        }

        if (null === $processor) {
            $processor = new Definition(PropertyAccessProcessor::class);

            //todo: try to recognize services before configuration
            if (true || $this->container->has('doctrine')) {
                $decorated = $processor;
                $processor = new Definition(RelationAwareProcessorDecorator::class);
                $processor->setArguments(
                    [
                        $decorated,
                        new Reference('doctrine'),
                    ]
                );
            }
        } else {
            $processor = new Reference($this->filterReference($processor));
        }

        $definition = new Definition(CreateController::class);
        $definition->setArguments(
            [
                $processor,
                $manager,
                $factory,
                new Reference('event_dispatcher'),
            ]
        );
        $definition->setPublic(true);

        $controllerId = $this->normalize('cruds.api.generated.'.$name.'.create_controller');
        $this->container->setDefinition($controllerId, $definition);

        $this->getLoaderDefinition()->addMethodCall(
            'addRoute',
            [
                $this->normalize('cruds_api_'.$name.'_create'),
                $path,
                $controllerId.':'.CreateController::ACTION,
                ['POST'],
            ]
        );
    }

    public function registerReadAction($name, $path, $repository)
    {
        $definition = new Definition(ReadController::class);
        $definition->setArguments(
            [
                $repository,
                new Reference('event_dispatcher'),
            ]
        );

        $controllerId = $this->normalize('cruds_api_'.$name.'_read_controller');
        $this->container->setDefinition($controllerId, $definition);

        $this->getLoaderDefinition()->addMethodCall(
            'addRoute',
            [
                $this->normalize('cruds_api_'.$name.'_read'),
                $path,
                $controllerId.':'.ReadController::ACTION,
                ['GET'],
            ]
        );
    }

    public function registerUpdateAction($name, $path, $repository, $processor, $manager)
    {
        if (null === $processor) {
            $processor = new Definition(PropertyAccessProcessor::class);

            //todo: try to recognize services before configuration
            if (true || $this->container->has('doctrine')) {
                $decorated = $processor;
                $processor = new Definition(RelationAwareProcessorDecorator::class);
                $processor->setArguments(
                    [
                        $decorated,
                        new Reference('doctrine'),
                    ]
                );
            }
        } else {
            $processor = new Reference($this->filterReference($processor));
        }

        $definition = new Definition(UpdateController::class);
        $definition->setArguments(
            [
                $repository,
                $processor,
                $manager,
                new Reference('event_dispatcher'),
            ]
        );

        $controllerId = $this->normalize('cruds_api_'.$name.'_update_controller');
        $this->container->setDefinition($controllerId, $definition);

        $this->getLoaderDefinition()->addMethodCall(
            'addRoute',
            [
                $this->normalize('cruds_api_'.$name.'_update'),
                $path,
                $controllerId.':'.UpdateController::ACTION,
                ['POST'],
            ]
        );
    }

    public function registerDeleteAction($name, $path, $repository, $manager)
    {
        $definition = new Definition(DeleteController::class);
        $definition->setArguments(
            [
                $repository,
                $manager,
                new Reference('event_dispatcher'),
            ]
        );

        $controllerId = $this->normalize('cruds_api_'.$name.'_delete_controller');
        $this->container->setDefinition($controllerId, $definition);

        $this->getLoaderDefinition()->addMethodCall(
            'addRoute',
            [
                $this->normalize('cruds_api_'.$name.'_delete'),
                $path,
                $controllerId.':'.DeleteController::ACTION,
                ['POST'],
            ]
        );
    }

    public function registerSearchAction($name, $path, $repository, array $filters = [])
    {
        $filterArray = [];
        foreach ($filters as $reference) {
            $filterArray[] = new Reference($this->filterReference($reference));
        }

        $definition = new Definition(SearchController::class);
        $definition->setArguments(
            [
                $repository,
                $filterArray,
                new Reference('event_dispatcher'),
            ]
        );

        $controllerId = $this->normalize('cruds_api_'.$name.'_search_controller');
        $this->container->setDefinition($controllerId, $definition);

        $this->getLoaderDefinition()->addMethodCall(
            'addRoute',
            [
                $this->normalize('cruds_api_'.$name.'_search'),
                $path,
                $controllerId.':'.SearchController::ACTION,
                ['GET', 'POST'],
            ]
        );
    }

    private function getLoaderDefinition()
    {
        return $this->container->getDefinition('cruds.api.router_loader');
    }

    private function normalize($name)
    {
        return str_replace('-', '_', $name);
    }

    /**
     * @param string $reference
     *
     * @return string
     */
    private function filterReference($reference)
    {
        return ltrim($reference, '@');
    }
}
