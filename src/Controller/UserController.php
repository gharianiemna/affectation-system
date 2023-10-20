<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;

class UserController extends AbstractController
{
private $userService;
public function __construct(UserService $userService){
    $this->userService=$userService;
}
    /**
     * @Rest\Post("/signin")
     */
    public function Register(Request $request): Response
    {
        dd("1");
    try {

        $this->userService->register($request);
        return new JsonResponse('ok');
    } catch (\Exception $e) {
        return new JsonResponse(['status' => 'fail', 'error' => $e->getMessage()], 400);
    }
}
}
