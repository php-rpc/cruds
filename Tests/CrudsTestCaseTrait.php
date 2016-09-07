<?php

namespace ScayTrase\Api\Cruds\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use ScayTrase\Api\Cruds\Tests\Fixtures\JmsSerializer\JmsTestKernel;
use ScayTrase\Api\Cruds\Tests\Fixtures\SymfonySerializer\SymfonyTestKernel;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @method static bootKernel
 */
trait CrudsTestCaseTrait
{
    /** @var  string */
    static protected $class;
    /** @var  KernelInterface */
    static protected $kernel;

    public function getKernelClasses()
    {
        return [
            'kernel with jms serializer'     => [JmsTestKernel::class],
            'kernel with symfony serializer' => [SymfonyTestKernel::class],
        ];
    }

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected static function configureDb()
    {
        static::bootKernel();
        /** @var EntityManagerInterface $em */
        $em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $tool     = new SchemaTool($em);
        $tool->dropDatabase();
        $tool->createSchema($metadata);
        $validator = new SchemaValidator($em);
        $errors    = $validator->validateMapping();
        static::assertCount(
            0,
            $errors,
            implode(
                "\n\n",
                array_map(
                    function ($l) {
                        return implode("\n\n", $l);
                    },
                    $errors
                )
            )
        );
    }

    protected static function setKernelClass($kernel)
    {
        self::$class = $kernel;
    }
}
