<?php

class Selim_TotalTimeSpent_Model_User extends XFCP_Selim_TotalTimeSpent_Model_User
{
    public function updateSessionActivity($userId, $ip, $controllerName, $action, $viewState, array $inputParams, $viewDate = null, $robotKey = '')
    {
        parent::updateSessionActivity($userId, $ip, $controllerName, $action, $viewState, $inputParams, $viewDate, $robotKey);
        if ($userId) {
            $visitor = XenForo_Visitor::getInstance();
            $oldViewDate = $visitor['old_view_date'];

            if (!$viewDate) {
                $viewDate = XenForo_Application::$time;
            }

            if ($oldViewDate == 0) {
                $oldViewDate = XenForo_Application::$time;
            }

            $spentTime = ($viewDate - $oldViewDate) + $visitor['spent_time'];

            try {
                $this->_getDb()->query('
				UPDATE xf_user
				SET
					old_view_date = ?,
					spent_time = ?
                WHERE user_id = ?

			', array($viewDate, $spentTime, $userId));
            } catch (Zend_Db_Exception $e) {
            }


        }
    }

    public function prepareUserConditions(array $conditions, array &$fetchOptions)
    {
        $result = parent::prepareUserConditions($conditions, $fetchOptions);

        if (!empty($conditions['spent_time']) && is_array($conditions['spent_time'])) {
            $result .= ' AND (' . $this->getCutOffCondition("user.spent_time", $conditions['spent_time']) . ')';
        }

        return $result;
    }

    public function prepareUserOrderOptions(array &$fetchOptions, $defaultOrderSql = '')
    {
        $choices = array(
            'spent_time' => 'user.spent_time'
        );
        $order = $this->getOrderByClause($choices, $fetchOptions);
        if ($order) {
            return $order;
        }

        return parent::prepareUserOrderOptions($fetchOptions, $defaultOrderSql);
    }

    public function appendSessionStart($userId, $sessionStart)
    {
        try {
            $this->_getDb()->query('
				UPDATE xf_user
				SET
				    old_view_date = ?
                WHERE user_id = ?

			', array($sessionStart, $userId));
        } catch (Zend_Db_Exception $e) {
        }
    }

    public function clearExpiredSessionActivities($cutOffDate = null)
    {
        if ($cutOffDate === null) {
            $cutOffDate = XenForo_Application::$time;
        }

        $db = $this->_getDb();
        $userSessions = $db->fetchAll('SELECT user_id FROM xf_user WHERE old_view_date <= ?', $cutOffDate);

        XenForo_Db::beginTransaction($db);

        foreach ($userSessions AS $userSession) {
            $db->update('xf_user',
                array('old_view_date' => 0),
                'user_id = ' . $db->quote($userSession['user_id'])
            );
        }

        XenForo_Db::commit($db);

    }

    public function clearSessionActivitiesForLogOut($userId)
    {
        if (!$userId) {
            return;
        }


        $db = $this->_getDb();
        XenForo_Db::beginTransaction($db);

        $db->update('xf_user',
            array('old_view_date' => 0),
            'user_id = ' . $db->quote($userId)
        );

        XenForo_Db::commit($db);
    }

}