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
use Symfony\Component\Validator\ConstraintViolationList;
class UserController extends AbstractController
{

    /**
     * @Rest\Post("/Register")
     */
    public function Register(Request $request, UserService $userService): Response
    {
        try {
            $result = $userService->register($request->getContent());
            if ($result instanceof JsonResponse) {
                return $result; 
            }
            return new JsonResponse(['result' => 'ok'], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'fail', 'error' => $e->getMessage()], 400);
        }
    }
}
