<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ScayTrase\Api\Cruds\Controller\CountController;
use ScayTrase\Api\Cruds\Criteria\EntityCriteriaConfigurator;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\PublicPropertyMapper;
use ScayTrase\Api\Cruds\ReferenceProviderInterface;
use ScayTrase\Api\Cruds\Tests\Fixtures\AbcClass;
use ScayTrase\Api\Cruds\Tests\Unit\AbstractControllerTest;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CountControllerTest extends TestCase
{
    public function testCounting()
    {
        $collection = new ArrayCollection();

        $f1 = $this->createFixture(1, 'b', null);
        $f2 = $this->createFixture(2, '5', '10');
        $f3 = $this->createFixture(3, 5, null);
        $f4 = $this->createFixture(4, null, 10);
        $collection->add($f1);
        $collection->add($f2);
        $collection->add($f3);
        $collection->add($f4);

        $configuration = new EntityCriteriaConfigurator(
            new PublicPropertyMapper(),
            $this->getReferenceProvider()
        );

        $evmProphecy = $this->prophesize(EventDispatcherInterface::class);
        $evmProphecy->dispatch(CrudEvents::COUNT, Argument::type(Event::class))->shouldBeCalled();
        /** @var EventDispatcherInterface $evm */
        $evm = $evmProphecy->reveal();

        $controller = new CountController(AbcClass::class, $collection, $configuration, $evm);

        self::assertSame(1, $controller->countAction(['b' => 5]));
        self::assertSame(1, $controller->countAction(['b' => '5']));
        self::assertSame(2, $controller->countAction(['c' => null]));
        self::assertSame(4, $controller->countAction(['a' => [1, 2, 3, 4]]));
        self::assertSame(3, $controller->countAction(['a' => [1, 2, 4]]));
    }

    private function createFixture($a, $b, $c)
    {
        $entity    = new AbcClass();
        $entity->a = $a;
        $entity->b = $b;
        $entity->c = $c;

        return $entity;
    }

    /**
     * @return ReferenceProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getReferenceProvider()
    {
        $mock = $this->getMockBuilder(ReferenceProviderInterface::class)->getMock();

        $mock->method('getEntityReference')->willReturnArgument(2);

        return $mock;
    }
}

