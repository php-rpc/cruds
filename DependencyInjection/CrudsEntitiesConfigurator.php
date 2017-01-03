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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
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
        $manager    = $config['manager'];
        $repository = $config['repository'];
        $mount      = $config['mount'];

        if (null === $manager) {
            $manager = new Definition(ObjectManager::class);
            $manager->setFactory([new Reference('doctrine'), 'getManagerForClass']);
            $manager->setArguments([$class]);
        } else {
            $manager = new Reference($this->filterReference($manager));
        }

        if (null === $repository) {
            $repositoryDefinition = new Definition(EntityRepository::class);
            $repositoryDefinition->setFactory([$manager, 'getRepository']);
            $repositoryDefinition->setArguments([$class]);
        } else {
            $repositoryDefinition = new Reference($this->filterReference($repository));
        }

        foreach ($actions as $action => $actionConfig) {
            if (!$actionConfig['enabled']) {
                continue;
            }

            $actionConfig['name']       = $name;
            $actionConfig['class']      = $class;
            $actionConfig['mount']      = $mount;
            $actionConfig['repository'] = $repositoryDefinition;
            $actionConfig['path']       = $prefix . $actionConfig['path'];
            $actionConfig['manager']    = $manager;
            $actionConfig['prefix']     = $prefix;
            $function                   = new \ReflectionMethod($this, 'register' . ucfirst($action) . 'Action');
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

    /**
     * @param string $reference
     *
     * @return string
     */
    private function filterReference($reference)
    {
        return ltrim($reference, '@');
    }

    public function registerCreateAction($mount, $name, $class, $factory, $processor, $path, $manager)
    {
        if (null === $factory) {
            $factory = new DefinitionDecorator('cruds.factory.reflection');
            $factory->setArguments([$class, []]);
        } else {
            $factory = new Reference($this->filterReference($factory));
        }

        if (null === $processor) {
            $processor = new Reference('cruds.processor.property_access');
        } else {
            $processor = new Reference($this->filterReference($processor));
        }

        $definition = new Definition(CreateController::class);
        $definition->setArguments(
            [
                $processor,
                $manager,
                $factory,
                $this->getEvm(),
            ]
        );
        $definition->setPublic(true);

        $actionName   = 'create';
        $controllerId = $this->generateControllerId($name, $actionName);
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

    /**
     * @return Reference
     */
    private function getEvm()
    {
        return new Reference('event_dispatcher');
    }

    /**
     * @param string $name
     * @param string $actionName
     *
     * @return string
     */
    private function generateControllerId($name, $actionName)
    {
        return $this->normalize('cruds.generated_controller.' . $name . '.' . $actionName);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function normalize($name)
    {
        return str_replace('-', '_', $name);
    }

    /**
     * @param string $mount
     * @param string $name
     * @param string $actionName
     * @param string $path
     * @param string $action
     * @param array  $methods
     * @param array  $options
     *
     * @return Definition
     * @throws \InvalidArgumentException
     */
    private function registerRoute($mount, $name, $actionName, $path, $action, array $methods, array $options = [])
    {
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
                        'mount'  => $mount,
                    ],
                    $options
                ),
            ]
        );
    }

    private function getLoaderDefinition()
    {
        return $this->container->getDefinition('cruds.api.router_loader');
    }

    public function registerReadAction($mount, $name, $path, $repository, $class)
    {
        $definition = new Definition(ReadController::class);
        $definition->setArguments(
            [
                $repository,
                $this->getEvm(),
            ]
        );

        $actionName   = 'read';
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
        $definition->setArguments(
            [
                $repository,
                $processor,
                $manager,
                $this->getEvm(),
            ]
        );

        $actionName   = 'update';
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
        $definition->setArguments(
            [
                $repository,
                $manager,
                $this->getEvm(),
            ]
        );

        $actionName   = 'delete';
        $controllerId = $controllerId = $this->generateControllerId($name, $actionName);
        $this->container->setDefinition($controllerId, $definition);

        $action = $controllerId . ':' . DeleteController::ACTION;
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

    public function registerSearchAction($mount, $name, $path, $class, $repository, $criteria, $count_path, $prefix)
    {

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
        $definition->setArguments(
            [
                $class,
                $repository,
                $criteriaConfigurator,
                $this->getEvm(),
            ]
        );

        $actionName   = 'search';
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
        $definition->setArguments(
            [
                $class,
                $repository,
                $criteriaConfigurator,
                $this->getEvm(),
            ]
        );

        $actionName   = 'count';
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
}
