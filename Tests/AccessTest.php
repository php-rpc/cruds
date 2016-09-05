<?php

namespace ScayTrase\Api\Cruds\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use ScayTrase\Api\Cruds\Tests\Fixtures\Entity\MyEntity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccessTest extends WebTestCase
{
    use CrudsTestCaseTrait;
    /** @var  EntityManagerInterface */
    public static $em;

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$kernel = static::createKernel([]);
        static::$kernel->boot();
        /** @var EntityManagerInterface $em */
        static::$em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $metadata = static::$em->getMetadataFactory()->getAllMetadata();
        $tool     = new SchemaTool(static::$em);
        $tool->dropDatabase();
        $tool->createSchema($metadata);
        $validator = new SchemaValidator(static::$em);
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

    public function testEntityRouting()
    {
        self::bootKernel();

        $this->doRequest('/api/entity/my-entity/create', 'POST', ['data' => ['publicApiField' => 'my-value']]);
        $this->doRequest('/api/entity/my-entity/read', 'GET', ['identifier' => 1]);
        $this->doRequest(
            '/api/entity/my-entity/update',
            'POST',
            [
                'identifier' => 1,
                'data'       => [
                    'publicApiField' => 'my-updated-value',
                    'parent'         => 1,
                ],
            ]
        );

        $this->doRequest('/api/entity/my-entity/search', 'GET', ['criteria' => []]);
        $this->doRequest('/api/entity/my-entity/delete', 'POST', ['identifier' => 1]);
    }

    private function doRequest($path, $method, array $args = [])
    {
        $client = self::createClient();
        $client->request($method, $path, $args);

        print($path);
        print(PHP_EOL);
        print((string)$client->getResponse());
        print(PHP_EOL);
        print(PHP_EOL);
        print(PHP_EOL);
    }
}
