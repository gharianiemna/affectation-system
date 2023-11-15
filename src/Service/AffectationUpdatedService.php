<?php

namespace App\Service;

class AffectationUpdatedService
{
    public const DEB = 'deb';
    public const INTER = 'inter';
    public const EXPERT = 'expert';


    private static $userTaksInfo = [];

    private $beginner;
    private $intermediate;
    private $expert;

    public function __construct(Beginner $beginner, Intermediate $intermediate, Expert $expert)
    {
        $this->beginner = $beginner;
        $this->intermediate = $intermediate;
        $this->expert = $expert;
    }

    public function canHandleTask($user, $task): bool
    {
        if (!array_key_exists($user->getId(), self::$userTaksInfo)) {
            self::$userTaksInfo[$user->getId()] = [
                'countTasks' => 0,
                'startHour' => []
            ];
        }

        $maxTotalTasks = 0;
        $maxTaskByHour = 0;
        $state = false;

        $taskDifficulty = $task->getDifficulty();
        $taskType = $task->getType();
        $taskHour = $task->getStartDate()->format('H:i:s');
        $expertise = $user->getLevel();

        switch ($expertise) {
            case self::DEB:
                $difficulties = $this->beginner->getDifficultyByExpertise();
                $maxTaskByHour = $this->beginner->getMaxTaskByHour();
                $userTaskType = $this->beginner->getTaskType();
                $maxTotalTasks = $this->beginner->getMaxTotalTasks();
                break;

            case self::INTER:
                $difficulties = $this->intermediate->getDifficultyByExpertise();
                $maxTaskByHour = $this->intermediate->getMaxTaskByHour();
                $userTaskType = $this->intermediate->getTaskType();
                $maxTotalTasks = $this->intermediate->getMaxTotalTasks();
                break;

            case self::EXPERT:
                $difficulties = $this->expert->getDifficultyByExpertise();
                $maxTaskByHour = $this->expert->getMaxTaskByHour();
                $userTaskType = $this->expert->getTaskType();
                $maxTotalTasks = $this->expert->getMaxTotalTasks();
                break;

            default:
                $difficulties = [];
                $maxTaskByHour = 0;
                $userTaskType = [];
                $maxTotalTasks = 0;
        }

        $nbrHour = count(array_filter(self::$userTaksInfo[$user->getId()]['startHour'], function ($hour) use ($taskHour) {
            return $hour == $taskHour;
        }));

        if (
            in_array($taskDifficulty, $difficulties) &&
            in_array($taskType, $userTaskType) &&
            self::$userTaksInfo[$user->getId()]['countTasks'] < $maxTotalTasks &&
            $nbrHour < $maxTaskByHour
        ) {
            self::$userTaksInfo[$user->getId()]['countTasks']++;
            self::$userTaksInfo[$user->getId()]['startHour'][] = $taskHour;
            $state = true;
        }

        return $state;
    }
}
