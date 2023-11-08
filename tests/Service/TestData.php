<?php
namespace App\Tests\Service;

class TestData
{
    public static function createTestUser($level)
    {
        $user = new User;
        $user->setFirstName('test')
            ->setLastName('test')
            ->setAge(30)
            ->setLevel($level)
            ->setPassword('12355')
            ->setUserName('ahmed');
        return $user;
    }

    public static function createTestTask($type, $difficulty, $startDate, $code, $userId)
    {
        $task = new Task;
        $task->setType($type)
            ->setDifficulty($difficulty)
            ->setName('aaaa')
            ->setStartDate(new \DateTime($startDate))
            ->setCode($code)
            ->setUserId($userId);
        return $task;
    }
}
