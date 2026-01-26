<?php

declare(strict_types=1);

namespace App\Tests\App\src\Infrastructure\Presentation\Api\User\Controller;

use App\Infrastructure\DataFixtures\Test\UserFixture;
use App\Infrastructure\Doctrine\Entity\User\DoctrineUser;
use App\Tests\ApiTestCase;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\HttpFoundation\Response;

/**
 * Функциональные тесты API‑контроллера User.
 */
class UserControllerTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures();
    }

    /**
     *  GET /users
     */
    public function testListAllUsers(): void
    {
        $this->client()->request('GET', '/users');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $data = $this->getResponseData();

        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);
        $this->assertCount(1, $data['data']);

        $user = $data['data'][0];
        $this->assertArrayHasKey('email', $user);
        $this->assertSame('john.doe@example.com', $user['email']);
    }

    /**
     *  GET /user/{id}
     */
    public function testShowExistingUser(): void
    {
        /** @var DoctrineUser $user */
        $user = $this->client()->getContainer()
            ->get('doctrine')
            ->getRepository(DoctrineUser::class)
            ->findOneBy(['email' => 'john.doe@example.com']);

        $this->assertNotNull($user, 'Фикстура не загрузилась');

        $this->client()->request('GET', '/user/'.$user->getId());

        self::assertResponseIsSuccessful();

        $data = $this->getResponseData();
        $this->assertArrayHasKey('data', $data);
        $this->assertSame('john.doe@example.com', $data['data']['email']);
    }

    public function testShowNonExistingUser(): void
    {
        $this->client()->request('GET', '/user/999999');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $data = $this->getResponseData();
        $this->assertArrayHasKey('error', $data);
        $this->assertSame('User not found', $data['error']);
    }

    /**
     *  POST /register
     */
    public function testRegisterSuccess(): void
    {
        $payload = [
            'email'    => 'alice@example.com',
            'password' => 'strong-pass',
            'role'     => ['ROLE_USER'],
        ];

        $this->client()->request('POST', '/register', $payload);
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $data = $this->getResponseData();

        $this->assertArrayHasKey('message', $data);
        $this->assertSame('User created', $data['message']);

        $user = $this->client()->getContainer()
            ->get('doctrine')
            ->getRepository(DoctrineUser::class)
            ->findOneBy(['email' => $payload['email']]);

        $this->assertNotNull($user);
        $this->assertSame(hash('sha256', $payload['password']), $user->getPassword());
    }

    public function testRegisterMissingFields(): void
    {
        $payload = ['email' => 'bob@example.com'];

        $this->client()->request('POST', '/register', $payload);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $data = $this->getResponseData();
        $this->assertArrayHasKey('error', $data);
        $this->assertSame('Missing fields', $data['error']);
    }

    public function testRegisterConflictWhenEmailExists(): void
    {
        $payload = [
            'email'    => 'john.doe@example.com',
            'password' => 'any',
            'role'     => ['ROLE_USER'],
        ];

        $this->client()->request('POST', '/register', $payload);

        self::assertResponseStatusCodeSame(Response::HTTP_CONFLICT);

        $data = $this->getResponseData();
        $this->assertArrayHasKey('error', $data);
        $this->assertNotEmpty($data['error']);
    }

    /**
     *  POST /update/{id}
     */
    public function testUpdateSuccess(): void
    {
        /** @var DoctrineUser $user */
        $user = $this->client()->getContainer()
            ->get('doctrine')
            ->getRepository(DoctrineUser::class)
            ->findOneBy(['email' => 'john.doe@example.com']);

        $payload = [
            'email' => 'john.renamed@example.com',
            'password' => 'new-password',
            'role' => ['ROLE_USER']
        ];

        $this->client()->request('POST', '/update/'.$user->getId(), $payload);

        self::assertResponseIsSuccessful();

        $data = $this->getResponseData();
        $this->assertArrayHasKey('message', $data);
        $this->assertSame('User created', $data['message']);

        $updated = $this->client()->getContainer()
            ->get('doctrine')
            ->getRepository(DoctrineUser::class)
            ->find($user->getId());

        $this->assertSame('john.renamed@example.com', $updated->getEmail());
    }

    public function testUpdateInvalidId(): void
    {
        $payload = [
            'email' => 'whatever@example.com',
            'password' => 'pass',
            'role' => ['ROLE_USER']
        ];

        $this->client()->request('POST', '/update/0', $payload);

        $statusCode = $this->client()->getResponse()->getStatusCode();

        $this->assertContains(
            $statusCode,
            [Response::HTTP_BAD_REQUEST, Response::HTTP_NOT_FOUND],
            "Expected 400 or 404, got {$statusCode}"
        );

        $data = $this->getResponseData();
        $this->assertArrayHasKey('error', $data);
    }

    public function testUpdateUserNotFound(): void
    {
        $payload = [
            'email' => 'notfound@example.com',
            'password' => 'pass',
            'role' => ['ROLE_USER']
        ];

        $this->client()->request('POST', '/update/999999', $payload);
        $statusCode = $this->client()->getResponse()->getStatusCode();
        $this->assertContains(
            $statusCode,
            [Response::HTTP_NOT_FOUND, Response::HTTP_CONFLICT],
            "Expected 404 or 409, got {$statusCode}"
        );

        $data = $this->getResponseData();
        $this->assertArrayHasKey('error', $data);
    }

    /**
     *  Вспомогательная загрузка фикстур
     */
    private function loadFixtures(): void
    {
        $fixtureClasses = [UserFixture::class];
        $em = $this->client()->getContainer()->get('doctrine')->getManager();

        $purger   = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $instances = array_map(fn(string $c) => new $c(), $fixtureClasses);

        $executor->execute($instances);
    }
}
