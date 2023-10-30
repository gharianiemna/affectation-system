<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use  Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class TaskService
{
    private $serializer;
    private $validator;
    private $entityManager;
    public function __construct(
    SerializerInterface $serializer, 
    ValidatorInterface $validator, 
    EntityManagerInterface $entityManager)
    {
        $this->serializer=$serializer;
        $this->validator = $validator;
        $this->entityManager=$entityManager;
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

    public function removeTask($task){
        $this->entityManager->remove($task);
        $this->entityManager->flush();
        return new JsonResponse(['result' => 'success'], 200);
    }

    public function updateTask($data){
    }
}
