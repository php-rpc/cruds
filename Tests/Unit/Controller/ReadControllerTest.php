<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Prophecy\Argument;
use ScayTrase\Api\Cruds\Controller\ReadController;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\Tests\Fixtures\AbcClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ReadControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $id = 241;
        $f1 = new AbcClass();

        $evm = $this->prophesize(EventDispatcherInterface::class);
        $evm->dispatch(CrudEvents::READ, Argument::type(CollectionCrudEvent::class))->shouldBeCalled();

        $repository = $this->prophesize(ObjectRepository::class);
        $repository->find(Argument::exact($id))->willReturn($f1)->shouldBeCalled();

        $controller = new ReadController($repository->reveal(), $evm->reveal());

        /** @var AbcClass $entity */
        $entity = $controller->getAction($id);
        self::assertSame($f1, $entity);
    }
}
