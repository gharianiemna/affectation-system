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

class AffectationTaskUserService
{   const DEB = 'deb';
    const INTER = 'inter';
    const EXPERT = 'expert';
    const MIGRATION ='migration';
    const PORTABILITE= 'portabilité';
    const INSTALLATION= 'installation';
    private $entityManager;
    private $userRepository;
    private $taskRepository;


    
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        TaskRepository $taskRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
    }

    public function canHandleTask($user, $task, $date): bool
    {   
        $userAssignedTaskList=[];
        $maxTotalTasks = 0;
        $currentTaskHours = [];
        $countTasksAffected = 0;
        $maxTaskByHour=0;
        $countUserAssignedTasks=0;
        $taskDifficulty=$task->getDifficulty();
        $expertise = $user->getLevel();
        $difficulties  = $this->getDifficultyByExpertise($expertise);
        $maxTaskByHour=$this->getMaxTaskByHour($expertise);
        $TaskType=$task->getType();
        $userTaskType=$this->getTaskType($expertise);
        if (in_array($taskDifficulty, $difficulties)) {
            if (in_array($TaskType, $userTaskType)) {
                $maxTotalTasks = $this->getMaxTotalTasks($expertise);
                $userAssignedTaskList = $this->taskRepository->findByUserToday($user, $date);
                // variable globale pour traçer les heures prises de chaque user sans passer ripository
                $countUserAssignedTasks = count($userAssignedTaskList);
                if ($countUserAssignedTasks < $maxTotalTasks) {
                    $taskHour = $task->getStartDate()->format('H:i:s');
                    $dateTime = $task->getStartDate()->format('Y-m-d H:i:s');
                    $currentTaskByHour = $this->taskRepository->findByUserNow($user, $dateTime);
                    if (count($currentTaskByHour) < $maxTaskByHour) {
                        $currentTaskByHour++;
                        $countUserAssignedTasks++;
                        return true;
                    }
                }
            }
        }else{
            return false;
        }
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
    private function getTaskType($expertise)
    {
        if (($expertise === self::DEB)) {
            return [self::MIGRATION];
        } elseif ($expertise === self::INTER) {
            return [self::INSTALLATION];
        } elseif ($expertise === self::EXPERT) {
        return [self::MIGRATION, self::PORTABILITE,  self::INSTALLATION];
        }
        return [];
    }

}
