<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Prophecy\Argument;
use ScayTrase\Api\Cruds\Controller\CreateController;
use ScayTrase\Api\Cruds\EntityProcessorInterface;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\ReflectionConstructorFactory;
use ScayTrase\Api\Cruds\Tests\Fixtures\AbcClass;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class CreateControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testEntityCreation()
    {
        $evm       = $this->createEvm();
        $factory   = $this->createConstructorFactory();
        $processor = $this->createProcessor(AbcClass::class);
        $manager   = $this->createEntityManager();

        $controller = new CreateController($processor, $manager, $factory, $evm);

        /** @var AbcClass $entity */
        $entity = $controller->createAction(['a' => 1, 'b' => 'b', 'c' => [1, 2, 3], 'd' => null]);
        self::assertInstanceOf(AbcClass::class, $entity);
        self::assertSame(1, $entity->a);
        self::assertSame('b', $entity->b);
        self::assertSame([1, 2, 3], $entity->c);
        self::assertNull(null, $entity->d);
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function createEvm()
    {
        $evmProphecy = $this->prophesize(EventDispatcherInterface::class);
        $evmProphecy->dispatch(CrudEvents::READ, Argument::type(CollectionCrudEvent::class))->shouldBeCalled();
        $evmProphecy->dispatch(CrudEvents::CREATE, Argument::type(Event::class))->shouldBeCalled();

        return $evmProphecy->reveal();
    }

    /**
     * @return ReflectionConstructorFactory
     */
    protected function createConstructorFactory()
    {
        return new ReflectionConstructorFactory(AbcClass::class);
    }

    /**
     * @param string $fqcn
     *
     * @return EntityProcessorInterface
     */
    abstract protected function createProcessor($fqcn);

    /**
     * @return ObjectManager
     */
    protected function createEntityManager()
    {
        $manager = $this->prophesize(ObjectManager::class);
        $manager->persist(Argument::type(AbcClass::class))->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        return $manager->reveal();
    }
}
