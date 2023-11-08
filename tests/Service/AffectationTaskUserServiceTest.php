<?php
namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use App\Entity\Task;
use App\Repository\UserRepository;
use App\Service\UserService;
use App\Entity\User;
use App\Service\AffectationTaskUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AffectationTaskUserServiceTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();
    }
/**
 * @return int
 */
    public function testCanHandleTask()
    {

    $userRepository = $this->createMock(UserRepository::class);
    $taskRepository = $this->createMock(TaskRepository::class);
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $affectationTaskUserService = new AffectationTaskUserService($entityManager, $userRepository, $taskRepository);


    $userDeb = new User();
    $userDeb->setFirstName('test')
    ->setLastName('test')
    ->setAge(30)
    ->setLevel('deb')
    ->setPassword('12355')
    ->setUserName('ahmed');

    $task = new Task();
    $task->setType('migration')
    ->setDifficulty(1)
    ->setName('aaaa')
    ->setStartDate(new \DateTime("2018-11-08 01:00:00"))
    ->setCode('xx-rsta-xx');

    $date = new \DateTime();
    $result = $affectationTaskUserService->canHandleTask($task, $date);
    $userRepository->method('findByLevel')->willReturn([$userDeb]);
    $this->assertTrue($result['state']);
    $this->assertSame($user, $result['user']);
}
}
