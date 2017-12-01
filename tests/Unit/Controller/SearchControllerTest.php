<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ScayTrase\Api\Cruds\Controller\SearchController;
use ScayTrase\Api\Cruds\Criteria\EntityCriteriaConfigurator;
use ScayTrase\Api\Cruds\Event\CollectionCrudEvent;
use ScayTrase\Api\Cruds\Event\CrudEvents;
use ScayTrase\Api\Cruds\PublicPropertyMapper;
use ScayTrase\Api\Cruds\ReferenceProviderInterface;
use ScayTrase\Api\Cruds\Tests\Fixtures\AbcClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SearchControllerTest extends TestCase
{
    public function testSearching()
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
        $evmProphecy->dispatch(CrudEvents::READ, Argument::type(CollectionCrudEvent::class))->shouldBeCalled();
        /** @var EventDispatcherInterface $evm */
        $evm = $evmProphecy->reveal();

        $controller = new SearchController(AbcClass::class, $collection, $configuration, $evm);

        $result = $controller->findAction(['b' => 5], ['a' => Criteria::DESC], 1);

        self::assertCount(1, $result);
        self::assertNotContains($f1, $result);
        self::assertNotContains($f2, $result);
        self::assertContains($f3, $result);
        self::assertNotContains($f4, $result);

        $result = $controller->findAction(['b' => '5'], ['a' => Criteria::DESC], 1);

        self::assertCount(1, $result);
        self::assertNotContains($f1, $result);
        self::assertContains($f2, $result);
        self::assertNotContains($f3, $result);
        self::assertNotContains($f4, $result);

        $result = $controller->findAction(['c' => null], ['a' => Criteria::ASC], 1);

        self::assertCount(1, $result);
        self::assertContains($f1, $result);
        self::assertNotContains($f2, $result);
        self::assertNotContains($f3, $result);
        self::assertNotContains($f4, $result);

        $result = $controller->findAction(['c' => null], ['a' => Criteria::ASC], 2);

        self::assertCount(2, $result);
        self::assertContains($f1, $result);
        self::assertNotContains($f2, $result);
        self::assertContains($f3, $result);
        self::assertNotContains($f4, $result);

        $result = $controller->findAction(['c' => null], ['a' => Criteria::ASC], 3);

        self::assertCount(2, $result);

        $result = $controller->findAction(['a' => [1, 2, 3, 4]], ['a' => Criteria::ASC], 2);

        self::assertCount(2, $result);
        self::assertContains($f1, $result);
        self::assertContains($f2, $result);
        self::assertNotContains($f3, $result);
        self::assertNotContains($f4, $result);

        $result = $controller->findAction(['a' => [1, 2, 3, 4]], ['a' => Criteria::ASC]);

        self::assertCount(4, $result);
        self::assertContains($f1, $result);
        self::assertContains($f2, $result);
        self::assertContains($f3, $result);
        self::assertContains($f4, $result);
        self::assertSame([$f1, $f2, $f3, $f4], $result->toArray());
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

