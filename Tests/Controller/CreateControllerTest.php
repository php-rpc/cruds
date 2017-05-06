<?php

namespace ScayTrase\Api\Cruds\Tests\Controller;

use ScayTrase\Api\Cruds\Tests\AbstractDbAwareTest;
use ScayTrase\Api\Cruds\Tests\Fixtures\Common\Entity\MyEntity;

class CreateControllerTest extends AbstractDbAwareTest
{
    public function testCreateAction()
    {
        $client = self::createClient();
        $client->request(
            'POST',
            '/api/entity/my-entity/create',
            [
                'data' => [
                    'public_api_field' => 'my-data',
                ],
            ],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json']
        );
        $response = $client->getResponse();

        self::assertTrue($response->isSuccessful());

        $em     = self::getEntityManager();
        $parent = $em->getRepository(MyEntity::class)->findOneBy(['publicApiField' => 'my-data']);
        self::assertNotNull($parent);

        $data = json_decode($response->getContent());

        self::assertEquals(JSON_ERROR_NONE, json_last_error());

        self::assertInstanceOf(\stdClass::class, $data);
        self::assertSame($parent->getId(), $data->id);
        self::assertObjectHasAttribute('public_api_field', $data);
        self::assertSame('my-data', $data->public_api_field);
        self::assertObjectNotHasAttribute('private_field', $data);
        self::assertNull($data->parent);
        self::assertSame([], $data->children);

        $client->request(
            'POST',
            '/api/entity/my-entity/create',
            [
                'data' => [
                    'public_api_field' => 'my-child-data',
                    'parent'           => $parent->getId(),
                ],
            ],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json']
        );
        $response = $client->getResponse();

        self::assertTrue($response->isSuccessful());
        $entity = $em->getRepository(MyEntity::class)->findOneBy(['publicApiField' => 'my-child-data']);
        self::assertNotNull($parent);

        $data = json_decode($response->getContent());

        self::assertEquals(JSON_ERROR_NONE, json_last_error());

        self::assertInstanceOf(\stdClass::class, $data);
        self::assertSame($entity->getId(), $data->id);
        self::assertSame('my-child-data', $data->public_api_field);
        self::assertObjectNotHasAttribute('private_field', $data);
        self::assertSame($parent->getId(), $data->parent);
        self::assertSame([], $data->children);

        $em->clear();
        $entity = $em->getRepository(MyEntity::class)->findOneBy(['publicApiField' => 'my-child-data']);
        $parent = $em->getRepository(MyEntity::class)->findOneBy(['publicApiField' => 'my-data']);

        self::assertSame($parent, $entity->getParent());
        self::assertContains($entity, $parent->getChildren());
    }
}
