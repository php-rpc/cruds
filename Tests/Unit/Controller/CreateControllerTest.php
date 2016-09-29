<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Prophecy\Argument;
use ScayTrase\Api\Cruds\Controller\CreateController;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\PropertyAccessProcessor;
use ScayTrase\Api\Cruds\ReflectionConstructorFactory;
use ScayTrase\Api\Cruds\Tests\Fixtures\AbcClass;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testEntityCreation()
    {
        $evmProphecy = $this->prophesize(EventDispatcherInterface::class);
        $evmProphecy->dispatch(CrudEvents::READ, Argument::type(CollectionCrudEvent::class))->shouldBeCalled();
        $evmProphecy->dispatch(CrudEvents::CREATE, Argument::type(Event::class))->shouldBeCalled();
        /** @var EventDispatcherInterface $evm */
        $evm = $evmProphecy->reveal();

        $factory   = new ReflectionConstructorFactory(AbcClass::class);
        $processor = new PropertyAccessProcessor();

        $manager = $this->prophesize(ObjectManager::class);
        $manager->persist(Argument::type(AbcClass::class))->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        $controller = new CreateController($processor, $manager->reveal(), $factory, $evm);

        /** @var AbcClass $entity */
        $entity = $controller->createAction(['a' => 1, 'b' => 'b', 'c' => [1, 2, 3], 'd' => null]);
        self::assertInstanceOf(AbcClass::class, $entity);
        self::assertSame(1, $entity->a);
        self::assertSame('b', $entity->b);
        self::assertSame([1, 2, 3], $entity->c);
        self::assertNull(null, $entity->d);
    }
}
