<?php

namespace ScayTrase\Api\Cruds\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use ScayTrase\Api\Cruds\Tests\Fixtures\JmsSerializer\JmsTestKernel;
use ScayTrase\Api\Cruds\Tests\Fixtures\SymfonySerializer\SymfonyTestKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractCrudsWebTest extends WebTestCase
{
    public function getKernelClasses()
    {
        return [
            'kernel with jms serializer'     => [JmsTestKernel::class],
            'kernel with symfony serializer' => [SymfonyTestKernel::class],
        ];
    }

    protected static function createAndBootKernel($class)
    {
        self::$class = $class;
        self::bootKernel();
    }

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected static function configureDb()
    {
        self::assertKernelBooted();

        /** @var EntityManagerInterface $em */
        $em = self::getEntityManager();

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

    protected static function assertKernelBooted()
    {
        if (null === self::$kernel || null === self::$kernel->getContainer()) {
            self::fail('Kernel is not booted');
        }
    }

    /**
     * @return EntityManagerInterface|object
     */
    protected static function getEntityManager()
    {
        return self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
}
