<?php

namespace ScayTrase\Api\Cruds\Tests\Unit\Processor;

use ScayTrase\Api\Cruds\Adaptors\Symfony\FormProcessor;
use ScayTrase\Api\Cruds\Adaptors\Symfony\MappedEntityFormFactory;
use ScayTrase\Api\Cruds\Exception\EntityProcessingException;
use ScayTrase\Api\Cruds\PublicPropertyMapper;
use ScayTrase\Api\Cruds\Tests\AbstractCrudsWebTest;
use ScayTrase\Api\Cruds\Tests\Fixtures\SymfonySerializer\SymfonyTestKernel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FormProcessorTest extends AbstractCrudsWebTest
{
    public function testProcessor()
    {
        self::createAndBootKernel(SymfonyTestKernel::class);
        $factory = static::$kernel->getContainer()->get('form.factory');

        $entity    = new SampleEntity();
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
            $processor->updateEntity($entity, ['b' => ['a' => [5, 42], 7], 'a' => '5']);

            self::fail('Should be invalid');
        } catch (EntityProcessingException $exception) {
        }
    }

    public function testProcessorFactory()
    {
        self::createAndBootKernel(SymfonyTestKernel::class);
        $factory = static::$kernel->getContainer()->get('form.factory');
        $mapper  = new PublicPropertyMapper();

        $entity = new SampleEntity();

        $processorFactory = new MappedEntityFormFactory($factory, $mapper);
        $form             = $processorFactory->createFormForClass(get_class($entity));

        self::assertTrue($form->has('a'));
        self::assertTrue($form->has('b'));
        self::assertTrue($form->has('c'));

        self::assertInstanceOf(IntegerType::class, $form->get('a')->getConfig()->getType()->getInnerType());
        self::assertInstanceOf(TextType::class, $form->get('b')->getConfig()->getType()->getInnerType());
        self::assertInstanceOf(CollectionType::class, $form->get('c')->getConfig()->getType()->getInnerType());
    }
}

class SampleEntity
{
    /** @var int */
    public $a;
    /** @var string */
    public $b;
    /** @var int[] */
    public $c = [];
}

class TestForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('a', TextType::class);
        $builder->add('b', IntegerType::class);
    }
}
