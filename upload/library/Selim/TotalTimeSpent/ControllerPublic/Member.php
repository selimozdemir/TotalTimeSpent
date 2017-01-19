<?php

class Selim_TotalTimeSpent_ControllerPublic_Member extends XFCP_Selim_TotalTimeSpent_ControllerPublic_Member
{
    public function actionIndex()
    {
        $response = parent::actionIndex();
        $type = $this->_input->filterSingle('type', XenForo_Input::STRING);
        if ($response instanceof XenForo_ControllerResponse_Reroute) {
            return $response;
        }

        if (!$this->_getUserModel()->canViewMemberList()) {
            return $this->responseNoPermission();
        }

        if ($type == 'online') {
            foreach ($response->params['users'] as $i => $user) {
                $response->params['users'][$i]['spent_time'] = $this->_getSecondsToHumanReadable($user['spent_time']);
            }

        }

        return $response;
    }

    public function actionMember()
    {
        $response = parent::actionMember();
        if ($response instanceof XenForo_ControllerResponse_Reroute) {
            return $response;
        } else {
            $response->params['user']['spent_time'] = $this->_getSecondsToHumanReadable($response->params['user']['spent_time']);
        }


        return $response;
    }

    protected function _getNotableMembers($type, $limit)
    {
        if ($type == 'online') {
            $userModel = $this->_getUserModel();
            $notableCriteria = array(
                'is_banned' => 0,
                'spent_time' => array('>', 0)

            );

            return array($userModel->getUsers($notableCriteria, array(
                'join' => XenForo_Model_User::FETCH_USER_FULL,
                'limit' => $limit,
                'order' => 'spent_time',
                'direction' => 'desc'
            )), 'spent_time');

        }

        return parent::_getNotableMembers($type, $limit);

    }

    protected function _getSecondsToHumanReadable($secs)
    {
        $units = array(
            "y" => 52 * 7 * 24 * 3600,
            "h" => 7 * 24 * 3600,
            "g" => 24 * 3600,
            "s" => 3600,
            "dk" => 60,
            "sn" => 1,
        );
        if ($secs == 0) return "0 sn";
        $s = "";
        foreach ($units as $name => $divisor) {
            if ($quot = intval($secs / $divisor)) {
                $s .= "$quot $name";
                $s .= ", ";
                $secs -= $quot * $divisor;
            }
        }

        return substr($s, 0, -2);
    }

}