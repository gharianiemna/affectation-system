<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;

class UserService
{
    private $entityManager;
    private $userRepository;
    private $userPasswordHasher;
    private $serializer;
    public function __construct(
    EntityManagerInterface $entityManager, 
    UserRepository $userRepository, 
    UserPasswordHasherInterface $userPasswordHasher, 
    SerializerInterface $serializer)
    {
        $this->entityManager=$entityManager;
        $this->userRepository=$userRepository;
        $this->userPasswordHasher=$userPasswordHasher;
        $this->serializer=$serializer;

    }
    public function register($data ){ 
        $user = $this->serializer->deserialize($data, User::class, 'json');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($user);  
        if (count($errors) > 0) {
            $firstError = $errors[0];
            $response = [
                'property' => $firstError->getPropertyPath(),
                'value' => $firstError->getInvalidValue(),
                'error' => $firstError->getMessage(),
            ];
            return new JsonResponse($response, 400);
        }
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return new JsonResponse(['result' => 'ok'], 201);
    }
}
