<?php

namespace ScayTrase\Api\Cruds\Adaptors\Symfony;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\ValueGuess;

final class PhpDocTypeGuesser implements FormTypeGuesserInterface
{
    /** @var string[] */
    private static $typeMap = [
        'int'     => IntegerType::class,
        'integer' => IntegerType::class,
        'string'  => TextType::class,
        'float'   => NumberType::class,
        'double'  => NumberType::class,
        'real'    => NumberType::class,
        'boolean' => CheckboxType::class,
        'bool'    => CheckboxType::class,
        'array'   => CollectionType::class,
    ];
    /** @var DocBlockFactory */
    private $factory;

    /**
     * PhpDocTypeGuesser constructor.
     */
    public function __construct()
    {
        $this->factory = DocBlockFactory::createInstance();
    }

    /** {@inheritdoc} */
    public function guessType($class, $property)
    {
        $annotation = $this->readPhpDocAnnotations($class, $property);

        if (null === $annotation) {
            return; // guess nothing if the @var annotation is not available
        }

        $type = $annotation->getType();


        if ($type instanceof Array_) {
            $entryType = TextType::class;
            if (!$type->getValueType() instanceof Mixed) {
                $entryType = $this->replaceType((string)$type->getValueType());
            }

            return new TypeGuess(CollectionType::class, ['entry_type' => $entryType], Guess::MEDIUM_CONFIDENCE);
        }

        if (!$this->exists((string)$type)) {
            return new TypeGuess(TextType::class, [], Guess::LOW_CONFIDENCE);
        }

        return new TypeGuess($this->replaceType((string)$type), [], Guess::MEDIUM_CONFIDENCE);
    }

    /**
     * @param string $class
     * @param string $property
     *
     * @return Var_
     */
    private function readPhpDocAnnotations($class, $property)
    {
        $reflectionProperty = new \ReflectionProperty($class, $property);
        $phpdoc             = $reflectionProperty->getDocComment();

        $docblock = $this->factory->create($phpdoc);

        $tags = $docblock->getTagsByName('var');

        return array_shift($tags);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function replaceType($type)
    {
        if (!$this->exists($type)) {
            return TextType::class;
        }

        return self::$typeMap[$type];
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function exists($type)
    {
        return array_key_exists($type, self::$typeMap);
    }

    /** {@inheritdoc} */
    public function guessRequired($class, $property)
    {
        return new ValueGuess(false, Guess::LOW_CONFIDENCE);
    }

    /** {@inheritdoc} */
    public function guessMaxLength($class, $property)
    {
    }

    /** {@inheritdoc} */
    public function guessPattern($class, $property)
    {
    }
}
