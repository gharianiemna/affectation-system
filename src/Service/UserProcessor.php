<?php

namespace App\Service;

abstract class UserProcessor
{
    abstract protected function getDifficultyByExpertise();

    abstract protected function getMaxTotalTasks();

    abstract protected function getMaxTaskByHour();

    abstract protected function getTaskType();
}
