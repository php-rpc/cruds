<?php

namespace ScayTrase\Api\Cruds\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;

abstract class AbstractDbAwareTest extends AbstractCrudsWebTest
{
    public function setUp()
    {
        parent::setUp();
        self::configureDb();
    }

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected static function configureDb()
    {
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

    /**
     * @return EntityManagerInterface|object
     */
    protected static function getEntityManager()
    {
        return self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
}
