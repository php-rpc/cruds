<?php

namespace ScayTrase\Api\Cruds\Tests\Controller;

use JMS\Serializer\Serializer;
use ScayTrase\Api\Cruds\Tests\AbstractCrudsWebTest;
use ScayTrase\Api\Cruds\Tests\Fixtures\Common\Entity\MyEntity;

class ReadControllerTest extends AbstractCrudsWebTest
{
    /**
     * @dataProvider getKernelClasses
     *
     * @param $kernel
     */
    public function testGetAction($kernel)
    {
        self::createAndBootKernel($kernel);
        self::configureDb();

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
            '/api/entity/my-entity/read',
            ['identifier' => $entity->getId()],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json']
        );
        $response = $client->getResponse();

        self::assertTrue($response->isSuccessful());
        $data = json_decode($response->getContent());

        self::assertEquals(JSON_ERROR_NONE, json_last_error());

        self::assertInstanceOf(\stdClass::class, $data);
        self::assertSame($entity->getId(), $data->id);
        self::assertSame('defaults', $data->public_api_field);
        self::assertObjectNotHasAttribute('private_field', $data);
        self::assertSame($parent->getId(), $data->parent);
        self::assertSame([], $data->children);

        $client->request(
            'GET',
            '/api/entity/my-entity/read',
            ['identifier' => $parent->getId()],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json']
        );
        $response = $client->getResponse();

        self::assertTrue($response->isSuccessful());
        $data = json_decode($response->getContent());

        self::assertEquals(JSON_ERROR_NONE, json_last_error());

        self::assertInstanceOf(\stdClass::class, $data);
        self::assertSame($parent->getId(), $data->id);
        self::assertSame('defaults', $data->public_api_field);
        self::assertObjectNotHasAttribute('private_field', $data);
        self::assertNull($data->parent);
        self::assertSame([$entity->getId()], $data->children);
    }
}
