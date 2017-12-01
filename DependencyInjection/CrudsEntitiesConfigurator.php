<?php

namespace ScayTrase\Api\Cruds\DependencyInjection;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use ScayTrase\Api\Cruds\Controller\CountController;
use ScayTrase\Api\Cruds\Controller\CreateController;
use ScayTrase\Api\Cruds\Controller\DeleteController;
use ScayTrase\Api\Cruds\Controller\ReadController;
use ScayTrase\Api\Cruds\Controller\SearchController;
use ScayTrase\Api\Cruds\Controller\UpdateController;
use ScayTrase\Api\Cruds\Criteria\NestedCriteriaConfigurator;
use ScayTrase\Api\Cruds\ReflectionConstructorFactory;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
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
        $class = $config['class'];
        $actions = $config['actions'];
        $prefix = $config['prefix'];
        $manager = $config['manager'];
        $repository = $config['repository'];
        $mount = $config['mount'];

        if (null === $manager) {
            $manager = 'cruds.class_' . $class . '.object_manager';
            $managerDef = new Definition(ObjectManager::class);
            $managerDef->setPublic(false);
            $managerDef->setFactory([new Reference('doctrine'), 'getManagerForClass']);
            $managerDef->setArguments([$class]);
            $this->container->setDefinition($manager, $managerDef);
        }
        $manager = new Reference($this->filterReference($manager));

        if (null === $repository) {
            $repository = 'cruds.class_' . $class . '.entity_repository';
            $repositoryDef = new Definition(EntityRepository::class);
            $repositoryDef->setPublic(false);
            $repositoryDef->setFactory([$manager, 'getRepository']);
            $repositoryDef->setArguments([$class]);
            $this->container->setDefinition($repository, $repositoryDef);
        }

        $repository = new Reference($this->filterReference($repository));

        foreach ($actions as $action => $actionConfig) {
            if (!$actionConfig['enabled']) {
                continue;
            }

            $actionConfig['name'] = $name;
            $actionConfig['class'] = $class;
            $actionConfig['mount'] = $mount;
            $actionConfig['repository'] = $repository;
            $actionConfig['path'] = $prefix . $actionConfig['path'];
            $actionConfig['manager'] = $manager;
            $actionConfig['prefix'] = $prefix;
            $function = new \ReflectionMethod($this, 'register' . ucfirst($action) . 'Action');
            $args = [];

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

    public function registerCreateAction($mount, $name, $class, $factory, $processor, $path, $manager)
    {
        $actionName = 'create';
        $controllerId = $this->generateControllerId($name, $actionName);

        if (null === $factory) {
            $factory = $controllerId . '.entity_factory';

            if (class_exists(ChildDefinition::class)) {
                $factoryDef = new ChildDefinition('cruds.factory.reflection');
            } else {
                $factoryDef = new DefinitionDecorator('cruds.factory.reflection');
            }

            $factoryDef->setArguments([$class, []]);
            $factoryDef->setPublic(false);
            $this->container->setDefinition($factory, $factoryDef);
        }

        $factory = new Reference($this->filterReference($factory));

        if (null === $processor) {
            $processor = 'cruds.processor.property_access';
        }

        $processor = new Reference($this->filterReference($processor));

        $definition = new Definition(CreateController::class);
        $definition->setPublic(true);
        $definition->setArguments(
            [
                $processor,
                $manager,
                $factory,
                $this->getEvm(),
            ]
        );
        $definition->setPublic(true);


        $this->container->setDefinition($controllerId, $definition);

        $action = $controllerId . ':' . CreateController::ACTION;
        $this->registerRoute(
            $mount,
            $name,
            $actionName,
            $path,
            $action,
            ['POST'],
            ['class' => $class, 'arguments' => ['data']]
        );
    }

    public function registerReadAction($mount, $name, $path, $repository, $class)
    {
        $definition = new Definition(ReadController::class);
        $definition->setPublic(true);
        $definition->setArguments(
            [
                $repository,
                $this->getEvm(),
            ]
        );

        $actionName = 'read';
        $controllerId = $this->generateControllerId($name, $actionName);
        $this->container->setDefinition($controllerId, $definition);

        $action = $controllerId . ':' . ReadController::ACTION;
        $this->registerRoute(
            $mount,
            $name,
            $actionName,
            $path,
            $action,
            ['GET', 'POST'],
            ['class' => $class, 'arguments' => ['identifier']]
        );
    }

    public function registerUpdateAction($mount, $name, $path, $repository, $processor, $manager, $class)
    {
        if (null === $processor) {
            $processor = new Reference('cruds.processor.property_access');
        } else {
            $processor = new Reference($this->filterReference($processor));
        }

        $definition = new Definition(UpdateController::class);
        $definition->setPublic(true);
        $definition->setArguments(
            [
                $repository,
                $processor,
                $manager,
                $this->getEvm(),
            ]
        );

        $actionName = 'update';
        $controllerId = $this->generateControllerId($name, $actionName);
        $this->container->setDefinition($controllerId, $definition);

        $action = $controllerId . ':' . UpdateController::ACTION;
        $this->registerRoute(
            $mount,
            $name,
            $actionName,
            $path,
            $action,
            ['POST', 'PATCH'],
            ['class' => $class, 'arguments' => ['identifier', 'data']]
        );
    }

    public function registerDeleteAction($mount, $name, $path, $repository, $manager, $class)
    {
        $definition = new Definition(DeleteController::class);
        $definition->setPublic(true);
        $definition->setPublic(true);
        $definition->setArguments(
            [
                $repository,
                $manager,
                $this->getEvm(),
            ]
        );

        $actionName = 'delete';
        $controllerId = $controllerId = $this->generateControllerId($name, $actionName);
        $this->container->setDefinition($controllerId, $definition);

        $action = $controllerId . ':' . DeleteController::ACTION;
        $definition->setPublic(true);
        $this->registerRoute(
            $mount,
            $name,
            $actionName,
            $path,
            $action,
            ['POST', 'DELETE'],
            ['class' => $class, 'arguments' => ['identifier']]
        );
    }

    public function registerSearchAction(
        string $mount,
        string $name,
        string $path,
        string $class,
        Reference $repository,
        $criteria,
        string $count_path,
        string $prefix
    ) {

        if (is_array($criteria)) {
            $filterArray = [];
            foreach ($criteria as $filter => $reference) {
                $filterArray[$filter] = new Reference($this->filterReference($reference));
            }
            $criteriaConfigurator = new Definition(NestedCriteriaConfigurator::class);
            $criteriaConfigurator->setArguments([$filterArray]);
        } else {
            $criteriaConfigurator = new Reference($this->filterReference($criteria));
        }

        $definition = new Definition(SearchController::class);
        $definition->setPublic(true);
        $definition->setArguments(
            [
                $class,
                $repository,
                $criteriaConfigurator,
                $this->getEvm(),
            ]
        );

        $actionName = 'search';
        $controllerId = $this->generateControllerId($name, $actionName);
        $this->container->setDefinition($controllerId, $definition);

        $action = $controllerId . ':' . SearchController::ACTION;
        $this->registerRoute(
            $mount,
            $name,
            $actionName,
            $path,
            $action,
            ['GET', 'POST'],
            ['class' => $class, 'arguments' => ['criteria', 'order', 'limit', 'offset']]
        );

        $definition = new Definition(CountController::class);
        $definition->setPublic(true);
        $definition->setArguments(
            [
                $class,
                $repository,
                $criteriaConfigurator,
                $this->getEvm(),
            ]
        );

        $actionName = 'count';
        $controllerId = $this->generateControllerId($name, $actionName);
        $this->container->setDefinition($controllerId, $definition);

        $action = $controllerId . ':' . CountController::ACTION;
        $this->registerRoute(
            $mount,
            $name,
            $actionName,
            $prefix . $count_path,
            $action,
            ['GET', 'POST'],
            ['class' => $class, 'arguments' => ['criteria']]
        );
    }

    private function filterReference(string $reference): string
    {
        return ltrim($reference, '@');
    }

    /**
     * @return Reference
     */
    private function getEvm(): Reference
    {
        return new Reference('event_dispatcher');
    }

    private function generateControllerId(string $name, string $actionName): string
    {
        return $this->normalize('cruds.generated_controller.' . $name . '.' . $actionName);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function normalize(string $name): string
    {
        return str_replace('-', '_', $name);
    }

    /**
     * @param string $mount
     * @param string $name
     * @param string $actionName
     * @param string $path
     * @param string $action
     * @param array $methods
     * @param array $options
     *
     * @return Definition
     * @throws \InvalidArgumentException
     */
    private function registerRoute(
        string $mount,
        string $name,
        string $actionName,
        string $path,
        string $action,
        array $methods,
        array $options = []
    ): Definition {
        return $this->getLoaderDefinition()->addMethodCall(
            'addRoute',
            [
                $mount,
                $this->normalize('cruds.routing.' . $name . '.' . $actionName),
                $path,
                $action,
                $methods,
                array_replace(
                    [
                        'action' => $actionName,
                        'mount' => $mount,
                    ],
                    $options
                ),
            ]
        );
    }

    private function getLoaderDefinition(): Definition
    {
        return $this->container->getDefinition('cruds.api.router_loader');
    }
}
