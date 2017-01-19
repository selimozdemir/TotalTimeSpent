<?php

class Selim_TotalTimeSpent_Install
{


    public static function installer()
    {
        self::addRemoveColumn('xf_user', 'spent_time', 'add', 'int(10) default 0', 'last_activity');
        self::addRemoveColumn('xf_user', 'old_view_date', 'add', 'int(10) default 0', 'spent_time');

    }

    public static function uninstaller()
    {
        self::addRemoveColumn('xf_user', 'spent_time', 'remove', 'int(10) default 0');
        self::addRemoveColumn('xf_user', 'old_view_date', 'remove', 'int(10) default 0');
    }

    public static function addRemoveColumn($tableName, $columnName, $action = 'remove', $fieldDef = NULL, $after = NULL)
    {
        $db = XenForo_Application::get('db');
        $exists = self::doesColumnExist($tableName, $columnName);

        if ($action == 'remove') {
            if ($exists) {
                $db->query("
					ALTER TABLE {$tableName} DROP COLUMN {$columnName}
					");
            }
        } elseif ($action == 'add') {
            if (!$exists) {
                $db->query("
					ALTER TABLE {$tableName} ADD {$columnName} {$fieldDef} AFTER {$after}
					");
            }
        }
    }

    public static function doesColumnExist($tableName, $columnName)
    {
        $db = XenForo_Application::get('db');

        return $db->fetchRow("
			SHOW COLUMNS
			FROM $tableName
			WHERE Field = ?
			", $columnName);
    }
}