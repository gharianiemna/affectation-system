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
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Filesystem\Exception\FileException;
use Symfony\Component\Process\Process;

class TaskController extends AbstractController
{
    /**
     * @Rest\Get("/api/getTasks", name="get_tasks")
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
    /**
     *@Rest\Post("/api/upload-excel", name="xlsx")
     * @param Request $request
     * @throws \Exception
    */

    public function uploadXslx(Request $request, TaskService $taskService)
    {
        $result = $taskService->uploadXslx($request);
        return new JsonResponse($result['message'], $result['code']);
    }

    // /**
    //  * @Rest\Post("/api/upload-excel", name="xlsx")
    //  * @param Request $request
    //  * @throws \Exception
    //  */
    // public function xslx(Request $request, KernelInterface $kernel)
    // {
    //     $file = $request->files->get('file');
    //     if (!$file) {
    //         return $this->json('No file uploaded.', 400);
    //     }
    //     $fileFolder = $this->getParameter('kernel.project_dir') . '\public\uploads'. DIRECTORY_SEPARATOR ;
    //     try {
    //         $filePathName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
    //         $file->move($fileFolder, $filePathName);
    //     } catch (FileException $e) {
    //         return $this->json('Error uploading file.', 500);
    //     }
    //     $filePath = $fileFolder . $filePathName;
    //     $process = new Process([
    //         'php', 
    //         'bin/console',
    //         'app:import-excel', 
    //         $filePath,
    //     ]);
    //     $process->run();

    //     // dump($process->getOutput());
    //     // dump($process->getErrorOutput());


    //     if ($process->isSuccessful()) {
    //         unlink($filePath);
    //         return $this->json('Data imported successfully.', 200);
    //     } else {
    //         unlink($filePath);
    //         return $this->json('Error importing data.', 500);
    //     }
    // } 
}

