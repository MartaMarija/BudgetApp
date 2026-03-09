<?php

namespace App\Tests\Api;

use App\Tests\Helper\ApiTestCase;

class UserTest extends ApiTestCase
{
    public function testRegister(): void
    {
        /** Arrange */
        $client = static::createClient();

        /** Act */
        $client->jsonRequest('POST', '/api/users/register', [
            'email' => 'john@gmail.com',
            'password' => 'password123',
            'firstName' => 'John',
            'lastName' => 'Doe',
        ]);

        /** Assert */
        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $userId = $data['data']['userId'];

        $this->assertNotEmpty($userId);
        $this->assertIsInt($userId);
    }
}
