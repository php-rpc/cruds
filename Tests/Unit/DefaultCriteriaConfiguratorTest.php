<?php

namespace ScayTrase\Api\Cruds\Tests\Unit;

use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ScayTrase\Api\Cruds\Criteria\DefaultCriteriaConfigurator;
use ScayTrase\Api\Cruds\CriteriaConfiguratorInterface;

class DefaultCriteriaConfiguratorTest extends TestCase
{
    public function testDefaultReplacesMissing()
    {
        $defaults = [
            'a' => 0,
            'b' => null,
            'c' => 'data',
            'e' => 'extra defaults',
        ];

        $provided = [
            'a' => 5,
            'c' => 'new data',
            'd' => 'extra data',
        ];

        $criteria = Criteria::create();
        $fqcn     = \stdClass::class;

        $decorated = $this->createConfigurator(
            $fqcn,
            $criteria,
            [
                'a' => 5,
                'b' => null,
                'c' => 'new data',
                'd' => 'extra data',
                'e' => 'extra defaults',
            ]
        );

        $default = new DefaultCriteriaConfigurator($decorated, $defaults);
        $default->configure($fqcn, $criteria, $provided);
    }


    private function createConfigurator($fqcn, Criteria $criteria, $data)
    {
        $mock = self::prophesize(CriteriaConfiguratorInterface::class);
        $mock->configure(Argument::exact($fqcn), Argument::exact($criteria), Argument::exact($data))
            ->shouldBeCalled();

        return $mock->reveal();
    }
}
