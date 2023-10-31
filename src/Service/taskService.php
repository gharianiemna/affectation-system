<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use  Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class TaskService
{
    private $serializer;
    private $validator;
    private $taskRepository;
    private $entityManager;
    public function __construct(
    SerializerInterface $serializer, 
    ValidatorInterface $validator, 
    EntityManagerInterface $entityManager,
    TaskRepository $taskRepository)
    {
        $this->serializer=$serializer;
        $this->validator = $validator;
        $this->entityManager=$entityManager;
        $this->taskRepository=$taskRepository;
    }

    public function addTask($data){
        $task = $this->serializer->deserialize($data, Task::class, 'json');
        $errors = $this->validator->validate($task);  
        if (count($errors) > 0) {
            $firstError = $errors[0];
            $response = [
                'property' => $firstError->getPropertyPath(),
                'value' => $firstError->getInvalidValue(),
                'error' => $firstError->getMessage(),
            ];
            return new JsonResponse($response, 400);
        }
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        return new JsonResponse(['result' => 'ok'], 201);
    }

    public function updateTask($data, $taskId): JsonResponse 
    {
        $currentTask = $this->taskRepository->find($taskId);
        if (!$currentTask) {
            return new JsonResponse(['error' => 'Task not found'], 404);
        }
        $updatedTask = $this->serializer->deserialize($data, 
            Task::class, 
            'json', 
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentTask]);

        $errors = $this->validator->validate($updatedTask);  
        if (count($errors) > 0) {
            $firstError = $errors[0];
            $response = [
                'property' => $firstError->getPropertyPath(),
                'value' => $firstError->getInvalidValue(),
                'error' => $firstError->getMessage(),
            ];
            return new JsonResponse($response, 400);
        }
                $this->entityManager->persist($updatedTask);
                $this->entityManager->flush();
        return new JsonResponse(['result' => 'success'], 200);
    }


   public function removeTask($task){
        $this->entityManager->remove($task);
        $this->entityManager->flush();
        return new JsonResponse(['result' => 'success'], 200);
    }
}
