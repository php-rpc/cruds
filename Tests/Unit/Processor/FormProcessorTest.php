<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Processor;

use ScayTrase\Api\Cruds\Adaptors\Symfony\FormProcessor;
use ScayTrase\Api\Cruds\Exception\EntityProcessingException;
use ScayTrase\Api\Cruds\Tests\AbstractCrudsWebTest;
use ScayTrase\Api\Cruds\Tests\Fixtures\SymfonySerializer\SymfonyTestKernel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FormProcessorTest extends AbstractCrudsWebTest
{
    public function testProcessor()
    {
        self::createAndBootKernel(SymfonyTestKernel::class);
        $factory = static::$kernel->getContainer()->get('form.factory');

        $entity    = new \stdClass();
        $entity->b = null;
        $entity->a = null;

        $processor = new FormProcessor($factory, TestForm::class);
        $processor->updateEntity($entity, ['a' => 'a', 'b' => 3]);

        self::assertSame('a', $entity->a);
        self::assertSame(3, $entity->b);


        $processor->updateEntity($entity, ['a' => 3, 'b' => '5']);

        self::assertSame('3', $entity->a);
        self::assertSame(5, $entity->b);


        try {
            $processor->updateEntity($entity, ['a' => ['a' => [5, 42], 7], 'b' => '5']);

            self::fail('Should be invalid');
        } catch (EntityProcessingException $exception) {
        }
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
