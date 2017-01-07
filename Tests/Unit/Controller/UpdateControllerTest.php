<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Prophecy\Argument;
use ScayTrase\Api\Cruds\Controller\UpdateController;
use ScayTrase\Api\Cruds\EntityProcessorInterface;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\ReflectionConstructorFactory;
use ScayTrase\Api\Cruds\Tests\Fixtures\AbcClass;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class UpdateControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdating()
    {
        $id       = 241;
        $original = new AbcClass();

        $controller = new UpdateController(
            $this->createRepository($id, $original),
            $this->createProcessor(AbcClass::class),
            $this->createEntityManager(),
            $this->createEvm()
        );

        /** @var AbcClass $entity */
        $entity = $controller->patchAction($id, ['a' => 1, 'b' => 'b', 'c' => [1, 2, 3], 'd' => null]);
        self::assertSame($original, $entity);
        self::assertSame(1, $entity->a);
        self::assertSame('b', $entity->b);
        self::assertSame([1, 2, 3], $entity->c);
        self::assertNull(null, $entity->d);
    }

    /**
     * @param mixed  $id
     * @param object $entity
     *
     * @return ObjectRepository
     */
    protected function createRepository($id, $entity)
    {
        $repository = $this->prophesize(ObjectRepository::class);
        $repository->find(Argument::exact($id))->willReturn($entity)->shouldBeCalled();

        return $repository->reveal();
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
        $manager->flush()->shouldBeCalled();

        return $manager->reveal();
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function createEvm()
    {
        $evmProphecy = $this->prophesize(EventDispatcherInterface::class);
        $evmProphecy->dispatch(CrudEvents::READ, Argument::type(CollectionCrudEvent::class))->shouldBeCalled();
        $evmProphecy->dispatch(CrudEvents::UPDATE, Argument::type(Event::class))->shouldBeCalled();

        return $evmProphecy->reveal();
    }

    /**
     * @return ReflectionConstructorFactory
     */
    protected function createConstructorFactory()
    {
        return new ReflectionConstructorFactory(AbcClass::class);
    }
}
