<?php

namespace App\Tests\Helper;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ApiTestCase extends WebTestCase
{
    protected function loginUser(KernelBrowser $client, string $email = 'test@example.com', string $password = 'password123'): string
    {
        $client->jsonRequest('POST', '/api/users/register', [
            'email' => $email,
            'password' => $password,
            'firstName' => 'John',
            'lastName' => 'Doe',
        ]);

        $client->jsonRequest('POST', '/api/login_check', [
            'email' => $email,
            'password' => $password,
        ]);

        return json_decode($client->getResponse()->getContent(), true)['token'];
    }
}