<?php

namespace ScayTrase\Api\Cruds\Tests\Controller;

use ScayTrase\Api\Cruds\Tests\WebTestCase;
use ScayTrase\Api\Cruds\Tests\Fixtures\Common\Entity\MyEntity;
use ScayTrase\Api\Cruds\Tests\Unit\StaticDbTestTrait;

class CountControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->loadFixtures([]);
    }

    public function testEmptyCriteria()
    {
        $this->createEntities();
        $this->doTest([], 2);
    }

    public function testRelationCriteria()
    {
        $parentId = $this->createEntities();
        $this->doTest(
            [
                'parent' => $parentId,
            ],
            1
        );
    }

    /**
     * @param $criteria
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    protected function doRequest($criteria)
    {
        $client = self::createClient();
        $client->request(
            'GET',
            '/api/entity/my-entity/count',
            [
                'criteria' => $criteria,
            ],
            [],
            ['HTTP_CONTENT_TYPE' => 'application/json']
        );

        return $client->getResponse();
    }

    private function doTest(array $criteria, $count)
    {
        $response = $this->doRequest($criteria);

        self::assertTrue($response->isSuccessful());
        $data = json_decode($response->getContent());

        self::assertEquals(JSON_ERROR_NONE, json_last_error());
        self::assertEquals($count, $data);
    }

    private function createEntities()
    {
        $em     = $this->getEntityManager();
        $entity = new MyEntity('my-test-secret');
        $em->persist($entity);
        $parent = new MyEntity('non-recursing-entity');
        $em->persist($parent);
        $entity->setParent($parent);
        $em->flush();
        $em->clear();

        return $parent->getId();
    }
}
