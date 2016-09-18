<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Processor;

use ScayTrase\Api\Cruds\Adaptors\Symfony\FormProcessor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class FormProcessorTest extends TypeTestCase
{
    public function testProcessor()
    {
        $entity    = new \stdClass();
        $entity->b = null;
        $entity->a = null;

        $processor = new FormProcessor($this->factory, TestForm::class);
        $processor->updateEntity($entity, ['a' => 3, 'b' => 'a']);

        self::assertSame('3', $entity->a);
        self::assertSame(null, $entity->b);

        $processor->updateEntity($entity, ['a' => 3, 'b' => '5']);

        self::assertSame('3', $entity->a);
        self::assertSame(5, $entity->b);

        $processor->updateEntity($entity, ['a' => ['a' => [5, 42], 7], 'b' => '5']);

        self::assertSame('3', $entity->a);
        self::assertSame(5, $entity->b);
    }
}

class TestForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('a', TextType::class);
        $builder->add('b', IntegerType::class);
    }
}
