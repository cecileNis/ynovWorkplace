<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\UserRepository;

class PrivateMessageTest extends ApiTestCase
{
  public function testPostPrivateMessageHasConversationMember()
  {
    $client = static::createClient();
    $client->disableReboot();
    $client->request('POST', '/api/users', ['json' => [
      'email' => 'test@example.com',
      'plainPassword' => 'string',
      'nickname' => 'string',
    ]]);

    $response = $client->request('POST', '/auth', ['json' => [
      'email' => 'test1@example.com',
      'password' => 'string',
    ]]);
    $this->assertResponseIsSuccessful();

    $data = $response->toArray();
    $token = $data['token'];
    $client->setDefaultOptions(['headers' => ['authorization' => 'Bearer ' . $token]]);

    // We create a new conversation
    $response = $client->request('POST', '/api/conversations', ['json' => [
      'guest' => '/api/users/1',
    ]]);
    $this->assertResponseIsSuccessful();

    $data = $response->toArray();
    $conversation = $data['@id'];

    $client->request('POST', '/api/private_messages', ['json' => [
      'content' => 'coucou',
      'conversation' => $conversation
    ]]);

    $this->assertResponseStatusCodeSame(201);
  }

  public function testPostPrivateMessageHasConversationNonMember()
  {
    $client = static::createClient();
    $client->disableReboot();
    $client->request('POST', '/api/users', ['json' => [
      'email' => 'test@example.com',
      'plainPassword' => 'string',
      'nickname' => 'string',
    ]]);

    $response = $client->request('POST', '/auth', ['json' => [
      'email' => 'test1@example.com',
      'password' => 'string',
    ]]);
    $this->assertResponseIsSuccessful();

    $data = $response->toArray();
    $token = $data['token'];
    $client->setDefaultOptions(['headers' => ['authorization' => 'Bearer ' . $token]]);

    $client->request('POST', '/api/private_messages', ['json' => [
      'content' => 'coucou',
      'conversation' => '/api/conversations/2'
    ]]);

    $this->assertResponseStatusCodeSame(403);
  }
}