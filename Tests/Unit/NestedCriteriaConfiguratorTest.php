<?php

namespace ScayTrase\Api\Cruds\Tests\Unit;

use Doctrine\Common\Collections\Criteria;
use Prophecy\Argument;
use ScayTrase\Api\Cruds\Criteria\NestedCriteriaConfigurator;
use ScayTrase\Api\Cruds\CriteriaConfiguratorInterface;

class NestedCriteriaConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIteratesNestedConfigurators()
    {
        $criteria = [
            'criteria1' => 'data1',
            'criteria2' => ['data2'],
            'criteria3' => null,
        ];

        $fqcn    = \stdClass::class;
        $crit    = Criteria::create();
        $filters = [];
        foreach ($criteria as $key => $value) {
            $filters[$key] = $this->createConfigurator($fqcn, $crit, $value);
        }

        $nested = new NestedCriteriaConfigurator($filters);
        $nested->configure($fqcn, $crit, $criteria);

    }

    /**
     * @expectedException \ScayTrase\Api\Cruds\Exception\NestedConfiguratorException
     */
    public function testExceptionThrownOnUnknownNesting()
    {
        $criteria = [
            'criteria1' => 'data1',
        ];

        $fqcn    = \stdClass::class;
        $crit    = Criteria::create();
        $filters = [];
        foreach ($criteria as $key => $value) {
            $filters[$key] = $this->createConfigurator($fqcn, $crit, $value, true);
        }

        $criteria['criteria2'] = 'data2';

        $nested = new NestedCriteriaConfigurator($filters);
        $nested->configure($fqcn, $crit, $criteria);
    }

    /**
     * @expectedException \ScayTrase\Api\Cruds\Exception\NestedConfiguratorException
     */
    public function testExceptionThrownInvalidDataSupplied()
    {
        $criteria = [
            'criteria1' => 'data1',
        ];

        $fqcn    = \stdClass::class;
        $crit    = Criteria::create();
        $filters = [];
        foreach ($criteria as $key => $value) {
            $filters[$key] = $this->createConfigurator($fqcn, $crit, $value, true);
        }

        $criteria = 'data2';

        $nested = new NestedCriteriaConfigurator($filters);
        $nested->configure($fqcn, $crit, $criteria);
    }


    /**
     *
     */
    public function testUnknownException()
    {

    }

    private function createConfigurator($fqcn, Criteria $criteria, $data, $e = false)
    {
        $mock   = self::prophesize(CriteriaConfiguratorInterface::class);
        $method = $mock->configure(Argument::exact($fqcn), Argument::exact($criteria), Argument::exact($data));

        if (!$e) {
            $method->shouldBeCalled();
        }

        return $mock->reveal();
    }
}
