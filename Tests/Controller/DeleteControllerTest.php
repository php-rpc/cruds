<?php

namespace ScayTrase\Api\Cruds\Tests\Controller;

use ScayTrase\Api\Cruds\Tests\Fixtures\Common\Entity\MyEntity;
use ScayTrase\Api\Cruds\Tests\WebTestCase;

final class DeleteControllerTest extends WebTestCase
{
    public function testDeleteAction()
    {
        $em     = $this->getEntityManager();
        $entity = new MyEntity('my-test-secret');
        $em->persist($entity);
        $parent = new MyEntity('non-recursing-entity');
        $em->persist($parent);
        $entity->setParent($parent);
        $em->flush();
        $em->clear();

        $client = self::createClient();
        $client->request(
            'POST',
            '/api/entity/my-entity/delete',
            ['identifier' => $entity->getId()],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json']
        );
        $response = $client->getResponse();

        self::assertTrue($response->isSuccessful());
        $data = json_decode($response->getContent());

        self::assertEquals(JSON_ERROR_NONE, json_last_error());
        self::assertNull($data);

        $em->clear();
        $parent = $em->find(MyEntity::class, $parent->getId());
        self::assertCount(0, $parent->getChildren());

        self::assertNull($em->find(MyEntity::class, $entity->getId()));
    }
}
