<?php

namespace ScayTrase\Api\Cruds\Tests;

class AccessTest extends AbstractCrudsWebTest
{
    /**
     * @dataProvider getKernelClasses
     *
     * @param $kernel
     */
    public function testEntityRouting($kernel)
    {
        self::createAndBootKernel($kernel);
        self::configureDb();

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

        echo($path);
        echo(PHP_EOL);
        echo((string)$client->getResponse());
        echo(PHP_EOL);
        echo(PHP_EOL);
        echo(PHP_EOL);
    }
}
