<?php

namespace App\Tests\Api;

use App\Tests\Helper\ApiTestCase;

class CategoryTest extends ApiTestCase
{
    public function testCreate(): void
    {
        /** Arrange */
        $client = static::createClient();
        $token = $this->loginUser($client);

        /** Act */
        $client->jsonRequest('POST', '/api/categories', ['name' => 'Food'], ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]);

        /** Assert */
        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $categoryId = $data['data']['item']['id'];

        $this->assertNotEmpty($categoryId);
        $this->assertIsInt($categoryId);
    }
}
