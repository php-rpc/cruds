<?php

namespace ScayTrase\Api\Cruds\Tests\Controller;

use ScayTrase\Api\Cruds\Tests\AbstractCrudsWebTest;
use ScayTrase\Api\Cruds\Tests\Fixtures\Common\Entity\MyEntity;

class UpdateControllerTest extends AbstractCrudsWebTest
{
    /**
     * @dataProvider getKernelClasses
     *
     * @param $kernel
     */
    public function testCreateAction($kernel)
    {
        self::createAndBootKernel($kernel);
        self::configureDb();

        $client = self::createClient();
        $em     = self::getEntityManager();

        $entity = new MyEntity('private-data');
        $entity->setPublicApiField('public-data');
        $em->persist($entity);
        $em->flush();
        $em->clear();


        $client->request(
            'POST',
            '/api/entity/my-entity/update',
            [
                'identifier' => $entity->getId(),
                'data'       => [
                    'public_api_field' => 'updated-data',
                    'parent'           => $entity->getId(),
                ],
            ],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json']
        );
        $response = $client->getResponse();

        self::assertTrue($response->isSuccessful());
        $em->clear();
        $entity = $em->getRepository(MyEntity::class)->find($entity->getId());
        self::assertSame('updated-data', $entity->getPublicApiField());
        self::assertSame($entity->getParent(), $entity);
        self::assertContains($entity, $entity->getChildren());
    }
}
