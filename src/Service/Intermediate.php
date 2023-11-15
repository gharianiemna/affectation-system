<?php

namespace App\Service;

class Intermediate extends UserProcessor
{

    public const INSTALLATION = 'installation';
    public function __construct()
    {

    }

    public function getDifficultyByExpertise()
    {
        return [3, 2, 1];
    }

    public function getMaxTotalTasks()
    {
        return 4;
    }

    public function getMaxTaskByHour()
    {
        return 1;
    }

    public function getTaskType()
    {
        return [self::INSTALLATION];
    }
}
