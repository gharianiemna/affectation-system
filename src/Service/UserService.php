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
use App\Repository\TaskRepository;
use App\Service\AffectationTaskUserService;

class UserService
{   const DEB = 'deb';
    const INTER = 'inter';
    const EXPERT = 'expert';
    const MIGRATION ='migration';
    const PORTABILITE= 'portabilité';
    const INSTALLATION= 'installation';
    private $entityManager;
    private $userRepository;
    private $taskRepository;
    private $userPasswordHasher;
    private $serializer;
    private $validator;
    private $affectationTaskUserService;
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        TaskRepository $taskRepository,
        AffectationTaskUserService $affectationTaskUserService
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->taskRepository = $taskRepository;
        $this->affectationTaskUserService=$affectationTaskUserService;
    }
    public function register($data)
    {
        $result = [];
        $user = $this->serializer->deserialize($data, User::class, 'json');
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $result = ['message' => (string)$errors, 'code' => 400];
        } else {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $result = ['message' => 'ok', 'code' => 201];
        }
        return $result;
    }

    public function taskAssign($date)
    {
        $dateObj = \DateTime::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        $userList = $this->userRepository->findAll();
        $taskList = $this->taskRepository->findByStartDate($dateObj);
        foreach ($taskList as $task) {
                foreach ($userList as $user) {
                    $canUserHandleTask = $this->affectationTaskUserService->canHandleTask($user, $task, $date);
                    // date ne pas passer mais dans canHandle retrieve from the startdate of the task
                    if($canUserHandleTask) {
                        $task->setUser($user);
                        $this->entityManager->persist($task);
                        $this->entityManager->flush();
                        break;
                }
            }
        }
        return ['message' => 'ok', 'code' => 201];
    }

}
