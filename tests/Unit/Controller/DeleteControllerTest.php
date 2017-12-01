<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ScayTrase\Api\Cruds\Controller\DeleteController;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\Tests\Fixtures\AbcClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DeleteControllerTest extends TestCase
{
    public function testDeleting()
    {
        $id = 241;
        $f1 = new AbcClass();

        $evm = $this->prophesize(EventDispatcherInterface::class);
        $evm->dispatch(CrudEvents::DELETE, Argument::type(CollectionCrudEvent::class))->shouldBeCalled();

        $repository = $this->prophesize(ObjectRepository::class);
        $repository->find(Argument::exact($id))->willReturn($f1)->shouldBeCalled();

        $manager = $this->prophesize(ObjectManager::class);
        $manager->remove(Argument::exact($f1))->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        $controller = new DeleteController($repository->reveal(), $manager->reveal(), $evm->reveal());
        $controller->deleteAction($id);
    }
}
