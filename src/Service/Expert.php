<?php

namespace App\Service;

class Expert extends UserProcessor
{
    public const MIGRATION = 'migration';
    public const PORTABILITE = 'portabilité';
    public const INSTALLATION = 'installation';
    public function __construct()
    {

    }

    public function getDifficultyByExpertise()
    {
        return [4, 3, 2, 1];
    }

    public function getMaxTotalTasks()
    {
        return 8;
    }

    public function getMaxTaskByHour()
    {
        return 2;
    }

    public function getTaskType()
    {
        return [self::MIGRATION, self::PORTABILITE, self::INSTALLATION];
    }
}
