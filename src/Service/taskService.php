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
use PhpOffice\PhpSpreadsheet\IOFactory;

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
    public function getTask(){
        return $this->taskRepository->findAll();
    }

    public function addTask($data){
        $result=[];
        $task = $this->serializer->deserialize($data, Task::class, 'json');
        $task->setDifficulty($this->taskReview($task));
        $errors = $this->validator->validate($task);  
        if (count($errors) > 0) {
            $result=['message'=>(string)$errors, 'code'=> 400];
        }else{
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $result=['message'=>'ok', 'code'=> 201];
        }
    return $result;
    }

    public function updateTask($data, $taskId){     
        $result=[];
        $currentTask = $this->taskRepository->find($taskId);
        if (!$currentTask) {
            $result=['message'=> 'Task not found', 'code'=> 404];
        }
        $updatedTask = $this->serializer->deserialize($data, 
            Task::class, 
            'json', 
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentTask]);
        $errors = $this->validator->validate($updatedTask);  
        if (count($errors) > 0) {
            $result=['message'=>(string)$errors, 'code'=> 400];
        }else{
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        $result=['message'=>'ok', 'code'=> 201];
        }
        return $result;
    }

    public function removeTask($task){
        $result=[];
        $this->entityManager->remove($task);
        $this->entityManager->flush();
        return  $result=['message'=>'ok', 'code'=> 200];
    }

    public function taskReview($task) {
        $type = $task->getType();
        $code = trim($task->getCode()); // Remove leading and trailing spaces
        $typeMappings = ['migration' => 1, 'installation' => 2, 'portabilité' => 4];
        $diff = $typeMappings[$type] ?? 0;
        $value = 0;
        if (str_contains($code, 'rsta')) {
            $value = 1;
        } elseif (str_contains($code, 'ftth')) {
            $value = 4;
        } elseif (str_starts_with($code, 'ot')) {
            $value = 2;
        } elseif (str_starts_with($code, 'as')) {
            $value = 3;
        }
        return max($diff, $value);
    }

    public function uploadXslx($request){
    $file = $request->files->get('file'); 
    $fileFolder = __DIR__ . '/../../public/uploads/';
    $filePathName = md5(uniqid()) . $file->getClientOriginalName();
                try {
                    $file->move($fileFolder, $filePathName);
                } catch (FileException $e) {
                    dd($e);
                }
        $spreadsheet = IOFactory::load($fileFolder . $filePathName); 
        $row = $spreadsheet->getActiveSheet()->removeRow(1); 
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    foreach ($sheetData as $Row) 
        { 
            $type = $Row['A']; 
            $difficulty = $Row['B']; 
            $name= $Row['C']; 
            $code = $Row['D']; 
            $startDate = $Row['E'];    
                $task = new Task(); 
                $task->setType($type);           
                $task->setDifficulty($difficulty);
                $task->setName($name);
                $task->setCode($code);
                $task->setStartDate(new \DateTime($startDate));
                $this->entityManager->persist($task); 
                $this->entityManager->flush(); 
        } 
        return  $result=['message'=>'ok', 'code'=> 201];
    }
}
