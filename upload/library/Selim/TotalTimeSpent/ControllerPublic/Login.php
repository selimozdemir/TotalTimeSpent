<?php

/**
 *  Copyright (c) XenForo Web
 *  Total Time Spent On Forums
 *  Author: Selim Özdemir
 *  Website: https://xenforo.web.tr/
 *  Contact: admin@xenforo.web.tr
 */
class Selim_TotalTimeSpent_ControllerPublic_Login extends XFCP_Selim_TotalTimeSpent_ControllerPublic_Login
{
    public function completeLogin($userId, $remember, $redirect, array $postData = array())
    {
        $response = parent::completeLogin($userId, $remember, $redirect, $postData);
        $session = XenForo_Application::getSession();
        $sessionStart = $session->get('sessionStart');

        $userModel = $this->_getUserModel();
        $userModel->appendSessionStart($userId, $sessionStart);

        return $response;
    }
}