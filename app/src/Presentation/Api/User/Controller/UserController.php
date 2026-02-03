<?php

namespace App\Presentation\Api\User\Controller;

use App\Application\Dto\PaginationParams;
use App\Application\User\Service\User\CreateUserService;
use App\Application\User\Service\User\GetAllUsersService;
use App\Application\User\Service\User\GetOneUserService;
use App\Application\User\Service\User\UpdateUserService;
use App\Presentation\Api\User\ViewModel\UserListViewModel;
use App\Presentation\Api\User\ViewModel\UserViewModel;
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
        $page = max(1, (int)$request->request->get('page', '1'));
        $limit = max(1, min(100, (int)$request->request->get('limit', '10')));

        $params = new PaginationParams($page, $limit);
        $paginatedResult = ($this->usersService)($params);

        $viewModel = new UserListViewModel(
            $paginatedResult,
            $page,
            $limit
        );

        return new JsonResponse($viewModel->toArray(), Response::HTTP_OK);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data = $request->request->all();

        if (!isset($data['email'], $data['password'], $data['role'], $data['index'], $data['street'])) {
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

        return new JsonResponse(['message' => 'User updated'], Response::HTTP_OK);
    }

    #[Route('/user/{id}', name: 'user', methods: ['GET'])]
    public function view(int $id): JsonResponse
    {
        $user = ($this->userService)($id);

        if (null === $user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $viewModel = new UserViewModel($user);
        return new JsonResponse(['data' => $viewModel->toArray()], Response::HTTP_OK);
    }
}
