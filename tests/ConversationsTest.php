<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class ConversationsTest extends ApiTestCase
{
    public function testCreateConversation()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->request('POST', '/api/users', ['json' => [
            'email' => 'test@example.com',
            'plainPassword' => 'string',
            'nickname' => 'string',
        ]]);

        $response = $client->request('POST', '/auth', ['json' => [
            'email' => 'test@example.com',
            'password' => 'string',
        ]]);
        $this->assertResponseIsSuccessful();

        $data = $response->toArray();
        $token = $data['token'];
        $client->setDefaultOptions(['headers' => ['authorization' => 'Bearer ' . $token]]);

        $client->request('POST', '/api/conversations', ['json' => [
            'guest' => '/api/users/2',
        ]]);

        $this->assertResponseStatusCodeSame(201);
    }
}