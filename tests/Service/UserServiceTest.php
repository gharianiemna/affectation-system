<?php
namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\UserRepository;
use App\Service\UserService;


class userServiceTest extends KernelTestCase
{
    private $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();
        $this->userService = $kernel->getContainer()->get(UserService::class);
    }

    public function testRegister() {
        $data = [
            'firstName' => 'test',
            'lastName' => 'test',
            'age' => 30,
            'level' => 1,
            'username' => 'testTest',
            'password' => '<PASSWORD>'
        ];
        $result=$this->userService->register($data);
        $this->assertTrue($result);
    }
}