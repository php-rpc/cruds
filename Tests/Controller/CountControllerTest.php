<?php

namespace ScayTrase\Api\Cruds\Tests\Controller;

use ScayTrase\Api\Cruds\Tests\AbstractCrudsWebTest;
use ScayTrase\Api\Cruds\Tests\AbstractDbAwareTest;
use ScayTrase\Api\Cruds\Tests\Fixtures\Common\Entity\MyEntity;

class CountControllerTest extends AbstractDbAwareTest
{
    public function testCountAction()
    {
        $em     = self::getEntityManager();
        $entity = new MyEntity('my-test-secret');
        $em->persist($entity);
        $parent = new MyEntity('non-recursing-entity');
        $em->persist($parent);
        $entity->setParent($parent);
        $em->flush();
        $em->clear();

        $client = self::createClient();
        $client->request(
            'GET',
            '/api/entity/my-entity/count',
            [
                'criteria' => [],
            ],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json']
        );
        $response = $client->getResponse();

        self::assertTrue($response->isSuccessful());
        $data = json_decode($response->getContent());

        self::assertEquals(JSON_ERROR_NONE, json_last_error());
        self::assertEquals(2, $data);
    }

    public function testCountWithRelation()
    {
        $em     = self::getEntityManager();
        $entity = new MyEntity('my-test-secret');
        $em->persist($entity);
        $parent = new MyEntity('non-recursing-entity');
        $em->persist($parent);
        $entity->setParent($parent);
        $em->flush();
        $em->clear();

        $client = self::createClient();
        $client->request(
            'GET',
            '/api/entity/my-entity/count',
            [
                'criteria' => [
                    'parent' => $parent->getId(),
                ],
            ],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json']
        );
        $response = $client->getResponse();

        self::assertTrue($response->isSuccessful());
        $data = json_decode($response->getContent());

        self::assertEquals(JSON_ERROR_NONE, json_last_error());
        self::assertEquals(1, $data);
    }
}
