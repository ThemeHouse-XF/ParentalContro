<?php

class ThemeHouse_ParentalContro_CronEntry_ExpireSessions
{

    public static function expireSessions()
    {
        $db = XenForo_Application::get('db');

        $db->query(
            '
            UPDATE xf_user
            SET parent_session = \'\', parent_session_start = 0
            WHERE parent_session_start < ?
        ', XenForo_Application::$time - 30 * 60);
    } /* END expireSessions */
}