<?php

class Selim_TotalTimeSpent_Listener
{
    public static function extendController($class, array &$extend)
    {
        switch ($class) {
            case 'XenForo_ControllerPublic_Login':
                $extend[] = 'Selim_TotalTimeSpent_ControllerPublic_Login';
                break;
            case 'XenForo_ControllerPublic_Logout':
                $extend[] = 'Selim_TotalTimeSpent_ControllerPublic_Logout';
                break;
            case 'XenForo_ControllerPublic_Member':
                $extend[] = 'Selim_TotalTimeSpent_ControllerPublic_Member';
                break;
        }
    }

    public static function extendModel($class, array &$extend)
    {
        if ($class == 'XenForo_Model_User') {
            $extend[] = 'Selim_TotalTimeSpent_Model_User';
        }
    }

}