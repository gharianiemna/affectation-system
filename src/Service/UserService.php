<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserService
{
    private $entityManager;
    private $userRepository;
    private $userPasswordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher){
        $this->entityManager=$entityManager;
        $this->userRepository=$userRepository;
        $this->userPasswordHasher=$userPasswordHasher;

    }
public function register($data){
    $user = new User();
    $user->setFirstName($data['firstName'])
            ->setLastName($data['lastName'])
            ->setAge($data['age'])
            ->setLevel($data['level'])
            ->setUsername($data['username'])
            ->setRoles($data['roles'])
            ->setPassword($this->userPasswordHasher->hashPassword($user, $data['password']));  
    $this->entityManager->persist($user);
    $this->entityManager->flush();
}
}
