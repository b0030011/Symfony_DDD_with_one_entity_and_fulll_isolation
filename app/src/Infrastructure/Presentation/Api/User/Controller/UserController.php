<?php

namespace App\Infrastructure\Presentation\Api\User\Controller;

use App\Application\User\Service\User\CreateUserService;
use App\Application\User\Service\User\GetAllUsersService;
use App\Application\User\Service\User\GetOneUserService;
use App\Application\User\Service\User\UpdateUserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class UserController
{
    public function __construct(
        private CreateUserService  $createUserService,
        private GetAllUsersService $usersService,
        private GetOneUserService  $userService,
        private UpdateUserService  $updateUserService,
    ) {
    }

    #[Route('/users', name: 'users', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $data = ($this->usersService)();
        return new JsonResponse(['data' => $data], Response::HTTP_OK);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data = $request->request->all();

        if (!isset($data['email'], $data['password'], $data['role'])) {
            return new JsonResponse(['error' => 'Missing fields'], Response::HTTP_BAD_REQUEST);
        }

        try {
            ($this->createUserService)($data);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return new JsonResponse(['message' => 'User created'], Response::HTTP_CREATED);
    }

    #[Route('/update/{id}', name: 'update', methods: ['POST'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->request->all();

        try {
            ($this->updateUserService)($data, $id);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['message' => 'User created'], Response::HTTP_OK);
    }

    #[Route('/user/{id}', name: 'user', methods: ['GET'])]
    public function view(int $id): JsonResponse
    {
        $data = ($this->userService)($id);

        if ($data === null) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['data' => $data], Response::HTTP_OK);
    }



}
