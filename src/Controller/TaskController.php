<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\TaskService;
use App\Entity\Task;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

class TaskController extends AbstractController
{
    /**
     * @Rest\Post("/api/addTask")
     */
    public function addTask(Request $request, TaskService $taskService): Response
    {
        try {
            $result = $taskService->addTask($request->getContent());
            if ($result instanceof JsonResponse) {
                return $result; 
            }
            return new JsonResponse(['result' => 'ok'], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'fail', 'error' => $e->getMessage()], 400);
        }
    }



    /**
     * @Rest\Delete("/api/removeTask/{taskId}")
     */
    public function removeTask(Task $taskId, TaskService $taskService): Response
    {
        try {
        $result = $taskService->removeTask($taskId);
            return new JsonResponse(['result' => 'ok'], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'fail', 'error' => $e->getMessage()], 400);
        }
    }


        /**
     * @Rest\Put("/api/updateTask/{taskId}")
     */
    public function updateTask(Request $request, TaskService $taskService): Response
    {

    }

   
}

