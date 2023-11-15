<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\TaskService;
use App\Entity\Task;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Annotation\Groups;


class TaskController extends AbstractController
{
    /**
     * @Rest\Get("/getTasks", name="get_tasks")
     * @Groups({"task"})
     */
    public function getTask(TaskService $taskService): Response
    {
        $tasks = $taskService->getTask();
        return $this->json($tasks, 200, [], ['groups' => 'task']);
    }

    /**
     * @Rest\Post("/api/addTask")
     */
    public function addTask(Request $request, TaskService $taskService): Response
    {
        $result = $taskService->addTask($request->getContent());
        return new JsonResponse($result['message'], $result['code']);
    }

    /**
     * @Rest\Put("/api/updateTask/{taskId}")
     */
    public function updateTask(Request $request, TaskService $taskService, Task $taskId): Response
    {
        $result = $taskService->updateTask($request->getContent(), $taskId);
        return new JsonResponse($result['message'], $result['code']);
    }

    /**
     * @Rest\Delete("/api/removeTask/{taskId}")
     */
    public function removeTask(Task $taskId, TaskService $taskService): Response
    {
        $result = $taskService->removeTask($taskId);
        return new JsonResponse($result['message'], $result['code']);
    }

}

