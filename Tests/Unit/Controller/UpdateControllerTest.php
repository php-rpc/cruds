<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Prophecy\Argument;
use ScayTrase\Api\Cruds\Controller\UpdateController;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\PropertyAccessProcessor;
use ScayTrase\Api\Cruds\Tests\Fixtures\AbcClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdating()
    {
        $id = 241;
        $f1 = new AbcClass();

        $evm = $this->prophesize(EventDispatcherInterface::class);
        $evm->dispatch(CrudEvents::READ, Argument::type(CollectionCrudEvent::class))->shouldBeCalled();
        $evm->dispatch(CrudEvents::UPDATE, Argument::type(CollectionCrudEvent::class))->shouldBeCalled();

        $repository = $this->prophesize(ObjectRepository::class);
        $repository->find(Argument::exact($id))->willReturn($f1)->shouldBeCalled();

        $processor = new PropertyAccessProcessor();

        $manager = $this->prophesize(ObjectManager::class);
        $manager->flush()->shouldBeCalled();

        $controller = new UpdateController($repository->reveal(), $processor, $manager->reveal(), $evm->reveal());

        /** @var AbcClass $entity */
        $entity = $controller->patchAction($id, ['a' => 1, 'b' => 'b', 'c' => [1, 2, 3], 'd' => null]);
        self::assertSame($f1, $entity);
        self::assertSame(1, $entity->a);
        self::assertSame('b', $entity->b);
        self::assertSame([1, 2, 3], $entity->c);
        self::assertNull(null, $entity->d);
    }
}
