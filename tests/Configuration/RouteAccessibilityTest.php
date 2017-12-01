<?php

namespace ScayTrase\Api\Cruds\Tests\Configuration;

use ScayTrase\Api\Cruds\Tests\Fixtures\Common\Entity\MyEntity;
use ScayTrase\Api\Cruds\Tests\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

final class RouteAccessibilityTest extends WebTestCase
{
    /** @var Client */
    private static $client;

    public static function setUpBeforeClass()
    {
        self::$client = self::createClient();
    }

    public function getRequests()
    {
        return [
            'create' => ['/api/entity/my-entity/create', 'POST', ['data' => ['publicApiField' => 'my-value']]],
            'get'    => ['/api/entity/my-entity/get', 'GET', ['identifier' => 1]],
            'update' => [
                '/api/entity/my-entity/update',
                'POST',
                [
                    'identifier' => 1,
                    'data'       => [
                        'publicApiField' => 'my-updated-value',
                        'parent'         => null,
                    ],
                ],
            ],
            'search' => ['/api/entity/my-entity/search', 'GET', ['criteria' => ['id' => 1]]],
            'count'  => ['/api/entity/my-entity/count', 'GET', ['criteria' => ['id' => 1]]],
            'delete' => ['/api/entity/my-entity/delete', 'POST', ['identifier' => 1]],
        ];
    }

    /**
     * @dataProvider getRequests
     *
     * @param string $path
     * @param string $method
     * @param array  $args
     */
    public function testRequestWasSuccessful($path, $method, array $args = [])
    {
        self::$client->request($method, $path, $args);
        self::assertTrue(self::$client->getResponse()->isSuccessful());
    }

    protected function setUp()
    {
        $this->loadFixtures([]);
        $entity = new MyEntity();
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
