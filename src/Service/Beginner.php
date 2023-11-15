<?php

namespace App\Service;

class Beginner extends UserProcessor
{        
    public const MIGRATION = 'migration';

    public function __construct()
    {

    }

    public function getDifficultyByExpertise()
    {
        return [1];
    }

    public function getMaxTotalTasks()
    {
        return 2;
    }

    public function getMaxTaskByHour()
    {
        return 1;
    }

    public function getTaskType()
    {
        return [self::MIGRATION];
    }
}
