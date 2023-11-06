<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;


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

}
