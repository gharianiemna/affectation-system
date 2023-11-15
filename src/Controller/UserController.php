<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Annotation\Groups;

class UserController extends AbstractController
{

    /**
     * @Rest\Post("/Register")
     */
    public function Register(Request $request, UserService $userService): Response
    {
        $result = $userService->register($request->getContent());
        return new JsonResponse($result['message'], $result['code']);
    }
/**
 * @Rest\Post("/api/affect/{date}")
 */
public function affect(UserService $userService, string $date): Response
{
    $result = $userService->taskAssign($date);
    return new JsonResponse($result['message'], $result['code']);
}
    /**
     * @Rest\Get("/api/getUsers", name="get_users")
     * @Groups({"userList"})
     */
    public function getUserList(UserService $userService): Response
    {
        $users = $userService->getUsers();
        return $this->json($users, 200, [], ['groups' => 'userList']);
    }

    /**
     * @Rest\Get("/api/getUser/{id}", name="get_user_by_id")
     * @Groups({"userList"})
     */
    public function getUserById(UserService $userService, int $id): Response
    {
        $user = $userService->getUserById($id);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }

        return $this->json($user, 200, [], ['groups' => 'userList']);
    }
}
