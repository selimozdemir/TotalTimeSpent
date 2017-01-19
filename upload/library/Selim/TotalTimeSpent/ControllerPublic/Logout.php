<?php

class Selim_TotalTimeSpent_ControllerPublic_Logout extends XFCP_Selim_TotalTimeSpent_ControllerPublic_Logout
{
    public function actionIndex()
    {
        $csrfToken = $this->_input->filterSingle('_xfToken', XenForo_Input::STRING);

        $redirectResponse = $this->responseRedirect(
            XenForo_ControllerResponse_Redirect::SUCCESS,
            $this->getDynamicRedirect(false, false)
        );

        $userId = XenForo_Visitor::getUserId();
        if (!$userId) {
            return $redirectResponse;
        }

        if ($this->_noRedirect() || !$csrfToken) {
            // request is likely from JSON, probably XenForo.OverlayTrigger, so show a confirmation dialog
            return $this->responseView('XenForo_ViewPublic_LogOut', 'log_out');
        } else {

            $userModel = $this->getModelFromCache('XenForo_Model_User');
            $userModel->clearSessionActivitiesForLogOut($userId);
        }

        return parent::actionIndex();

    }
}