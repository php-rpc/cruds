<?php

namespace ScayTrase\Api\Cruds\Tests\Fixtures\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AbcFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('a', IntegerType::class);
        $builder->add('b', TextType::class);
        $builder->add(
            'c',
            CollectionType::class,
            [
                'entry_type'   => IntegerType::class,
                'allow_add'    => true,
                'allow_delete' => true,
            ]
        );
        $builder->add('d', TextType::class);
    }
}
