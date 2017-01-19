<?php

class Selim_TotalTimeSpent_CronEntry
{

    public static function runHourly()
    {
        $userModel = XenForo_Model::create('XenForo_Model_User');

        $cutOffDate = XenForo_Application::$time - 3600;
        $userModel->clearExpiredSessionActivities($cutOffDate);
    }


}