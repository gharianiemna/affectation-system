<?php
namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use App\Entity\Task;
use  Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class TaskServiceTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();
        
    }
// cas de test réel:
    // public function testAddTask() {
    //     $data = json_encode([
    //         "type" => "migration",
    //         "difficulty" =>1,
    //         "name" => "emnana",
    //         "code" => "xx-xxxx-xx",
    //         "startDate" => "2018-11-11" 
    // ])  ;
    //      $serializer = self::getContainer()->get(SerializerInterface::class);
    //      $validator = self::getContainer()->get(ValidatorInterface::class);
    //      $entityManager = self::getContainer()->get(EntityManagerInterface::class);
    //      $taskRepository = self::getContainer()->get(TaskRepository::class);
    //     $taskservice = new TaskService($serializer,$validator,$entityManager,$taskRepository) ;
    //     $result=$taskservice->addTask($data);
    //     $this->assertInstanceOf(JsonResponse::class, $result);
    // }

// cas de test mock
    public function testAddTask()
    {
        $data = json_encode([
            "type" => "migration",
            "difficulty" =>1,
            "name" => "emnana",
            "code" => "xx-xxxx-xx",
            "startDate" => "2018-11-11" 
        ])  ;
        
        $task = new Task();
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects($this->once())
            ->method('deserialize')
            ->willReturn(json_decode(json_encode([
            "type" => "migration",
            "difficulty" =>1,
            "name" => "emnana",
            "code" => "xx-xxxx-xx",
            "startDate" => "2018-11-11" 
        ])));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn([]);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function ($task) {
            });
        $taskRepository = $this->createMock(TaskRepository::class);
        $taskService = new TaskService($serializer, $validator, $entityManager, $taskRepository);
        $result = $taskService->addTask($data); 
        $this->assertInstanceOf(JsonResponse::class, $result);
    }

    // public function testRemoveTask()
    // {
    //     $task = new Task();
    //     $serializer = self::getContainer()->get(SerializerInterface::class);
    //     $validator = self::getContainer()->get(ValidatorInterface::class);
    //     $entityManager = self::getContainer()->get(EntityManagerInterface::class);
    //     $taskRepository = self::getContainer()->get(TaskRepository::class);
    //     $taskService = new TaskService($serializer, $validator, $entityManager, $taskRepository);
    //     $result = $taskService->removeTask($task); 
    //     $this->assertInstanceOf(JsonResponse::class, $result);
    // }
    public function testRemoveTask()
    {
        $task = new Task();
        $serializer = $this->createMock(SerializerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $taskRepository = $this->createMock(TaskRepository::class);
        $taskService = new TaskService($serializer, $validator, $entityManager, $taskRepository);
        $result = $taskService->removeTask($task); 
        $this->assertInstanceOf(JsonResponse::class, $result);
    }

    public function testUpdateTask()
    {
        $data = json_encode([
            "type" => "migration",
            "difficulty" =>1,
            "name" => "emnafdfgsdgna",
            "code" => "xx-xxxx-xx",
            "startDate" => "2018-11-11" 
        ])  ;
        
        $task = new Task();
        $serializer = self::getContainer()->get(SerializerInterface::class);
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $taskRepository = self::getContainer()->get(TaskRepository::class);
        $taskService = new TaskService($serializer, $validator, $entityManager, $taskRepository);
        $result = $taskService->updateTask($data, 50); 
        $this->assertInstanceOf(JsonResponse::class, $result);
    }
}