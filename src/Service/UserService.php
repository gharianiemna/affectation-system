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

class UserService
{   const DEB = 'deb';
    const INTER = 'inter';
    const EXPERT = 'expert';
    private $entityManager;
    private $userRepository;
    private $taskRepository;
    private $userPasswordHasher;
    private $serializer;
    private $validator;
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        TaskRepository $taskRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->taskRepository = $taskRepository;
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
        $result = [];
        $userAssignedTask=[];
        $maxTotalTasks = 0;
        $currentTaskByHour = 0;
        $maxTaskByHour=0;
        foreach ($userList as $user) {
            $countTasksAffected = 0;
            $expertise = $user->getLevel();
            $difficulties  = $this->getDifficultyByExpertise($expertise);
            $maxTotalTasks = $this->getMaxTotalTasks($expertise);
            $maxTaskByHour=$this->getMaxTaskByHour($expertise);
            $userAssignedTask = $this->taskRepository->findByUserToday($user, $dateObj);
            if (count($userAssignedTask) < $maxTotalTasks) {
                foreach ($difficulties  as $value) {
                    $tasks = $this->taskRepository->findByDifficulty($value, $dateObj);
                    foreach ($tasks as $task) { 
                        $taskHour = $task->getStartDate()->format('H:i:s');
                        $dateTime= $task->getStartDate()->format('Y-m-d H:i:s');
                        $currentTaskByHour=$this->taskRepository->findByUserNow( $user, $dateTime);
                        if(count($currentTaskByHour) < $maxTaskByHour) {
                            $task->setUser($user);
                            $this->entityManager->persist($task);
                            $this->entityManager->flush();
                            $countTasksAffected++;
                            $currentTaskByHour++;
                            if ($countTasksAffected === $maxTotalTasks) {
                                break;
                            }
                        }
                    }
                }
            }
        }
        return ['message' => 'ok', 'code' => 201];
    }

    private function getDifficultyByExpertise($expertise)
    {
        if ($expertise === self::DEB) {
            return [1];
        } elseif ($expertise === self::INTER) {
            return [3, 2, 1];
        } elseif ($expertise === self::EXPERT) {
            return [4, 3, 2, 1];
        }
        return [];
    }

    private function getMaxTotalTasks($expertise)
    {
        if ($expertise === self::DEB) {
            return 2;
        } elseif ($expertise === self::INTER) {
            return 4;
        } elseif ($expertise === self::EXPERT) {
            return 8;
        }
        return 0;
    }
    private function getMaxTaskByHour($expertise)
    {
        if (($expertise === self::DEB) || ($expertise === self::INTER)) {
            return 1;
        } elseif ($expertise === self::EXPERT) {
            return 2;
        }
        return 0;
    }


}
