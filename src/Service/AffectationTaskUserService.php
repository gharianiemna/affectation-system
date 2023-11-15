<?php

namespace App\Service;

class AffectationTaskUserService
{
    public const DEB = 'deb';
    public const INTER = 'inter';
    public const EXPERT = 'expert';
    public const MIGRATION = 'migration';
    public const PORTABILITE = 'portabilité';
    public const INSTALLATION = 'installation';

    private static $userTaksInfo = [];


    public function __construct() 
    {
    }

    public function canHandleTask($user, $task): bool
    {
        if(!array_key_exists($user->getId(), self::$userTaksInfo)) {
            self::$userTaksInfo[$user->getId()] = [];
            self::$userTaksInfo[$user->getId()]['countTasks'] = 0 ;
            self::$userTaksInfo[$user->getId()]['startHour'] = [] ;
        }

        $maxTotalTasks = 0;
        $maxTaskByHour = 0;
        $state = false;

        $taskDifficulty = $task->getDifficulty();
        $TaskType = $task->getType();
        $taskHour = $task->getStartDate()->format('H:i:s');
        $expertise = $user->getLevel();

        $difficulties  = $this->getDifficultyByExpertise($expertise);
        $maxTaskByHour = $this->getMaxTaskByHour($expertise);
        $userTaskType = $this->getTaskType($expertise);
        $maxTotalTasks = $this->getMaxTotalTasks($expertise);

        
        $nbrHour = count(array_filter(self::$userTaksInfo[$user->getId()]['startHour'], function ($hour) use ($taskHour) { return $hour == $taskHour; }));

        if (in_array($taskDifficulty, $difficulties) &&
            in_array($TaskType, $userTaskType) &&
            self::$userTaksInfo[$user->getId()]['countTasks'] < $maxTotalTasks &&
            $nbrHour < $maxTaskByHour) 
            {
            self::$userTaksInfo[$user->getId()]['countTasks']++;
            self::$userTaksInfo[$user->getId()]['startHour'][] = $taskHour;
            $state = true;
        }
        return $state;
    }

    private function getDifficultyByExpertise($expertise)
    {

        if ($expertise === self::DEB) {
            return [1];
        } elseif ($expertise === self::INTER) {
            return [3, 2, 1];
        } elseif ($expertise === self::EXPERT) {
            return [4, 3, 2, 1];
        }
        return [];
    }

    private function getMaxTotalTasks($expertise)
    {
        if ($expertise === self::DEB) {
            return 2;
        } elseif ($expertise === self::INTER) {
            return 4;
        } elseif ($expertise === self::EXPERT) {
            return 8;
        }
        return 0;
    }

    /**
     * retourne maximum taches par heure
     *
     * @param string $expertise
     * @return void
     */
    private function getMaxTaskByHour(string $expertise)
    {
        if (($expertise === self::DEB) || ($expertise === self::INTER)) {
            return 1;
        } elseif ($expertise === self::EXPERT) {
            return 2;
        }
        return 0;
    }
    private function getTaskType($expertise)
    {
        if (($expertise === self::DEB)) {
            return [self::MIGRATION];
        } elseif ($expertise === self::INTER) {
            return [self::INSTALLATION];
        } elseif ($expertise === self::EXPERT) {
            return [self::MIGRATION, self::PORTABILITE,  self::INSTALLATION];
        }
        return [];
    }

}
