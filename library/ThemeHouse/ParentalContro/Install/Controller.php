<?php

class ThemeHouse_ParentalContro_Install_Controller extends ThemeHouse_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/parental-control-by-th.3000/';

    protected function _getTables()
    {
        return array(
            'xf_parent_authenticate' => array(
                'parent_email' => 'VARCHAR(120) NOT NULL PRIMARY KEY', /* END 'parent_email' */
                'parent_scheme_class' => 'VARCHAR(75) NOT NULL', /* END 'parent_scheme_class' */
                'parent_data' => 'MEDIUMBLOB NOT NULL', /* END 'parent_data' */
            ), /* END 'xf_user_authenticate' */
            'xf_parent_field_lock' => array(
                'user_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'user_id' */
                'field_name' => 'VARCHAR(255) NOT NULL', /* END 'field_name' */
            ), /* END 'xf_parent_field_lock' */
            'xf_parent_revoked_permission' => array(
                'user_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'user_id' */
                'permission_group_id' => 'VARCHAR(25) NOT NULL', /* END 'permission_group_id' */
                'permission_id' => 'VARCHAR(25) NOT NULL', /* END 'permission_id' */
            ), /* END 'xf_parent_revoked_permissions' */
            'xf_parent_limit' => array(
                'user_id' => 'INT(10) UNSIGNED NOT NULL', /* END 'user_id' */
                'permission_group_id' => 'VARCHAR(25) NOT NULL', /* END 'permission_group_id' */
                'permission_id' => 'VARCHAR(25) NOT NULL', /* END 'permission_id' */
                'value_int' => 'INT(10) UNSIGNED NOT NULL', /* END 'value_int' */
            ), /* END 'xf_parent_limit' */
        );
    } /* END _getTables */

    protected function _getPrimaryKeys()
    {
        return array(
            'xf_parent_field_lock' => array(
                'user_id',
                'field_name'
            ), /* END 'xf_parent_field_lock' */
            'xf_parent_revoked_permission' => array(
                'user_id',
                'permission_group_id',
                'permission_id'
            ), /* END 'xf_parent_revoked_permission' */
            'xf_parent_limit' => array(
                'user_id',
                'permission_group_id',
                'permission_id'
            ), /* END 'xf_parent_limit' */
        );
    } /* END _getPrimaryKeys */

    protected function _getTableChanges()
    {
        return array(
            'xf_user' => array(
                'parent_email' => 'VARCHAR(120) NOT NULL DEFAULT \'\'', /* END 'parent_email' */
                'parental_control_state' => 'ENUM(\'enabled\',\'email_confirm\',\'email_confirm_edit\',\'disabled\') NOT NULL DEFAULT \'disabled\'',
                'parent_session' => 'VARBINARY(32) NOT NULL DEFAULT \'\'', /* END 'parent_session' */
                'parent_session_start' => 'INT(10) NOT NULL DEFAULT 0', /* END 'parent_session_start' */
            ), /* END 'xf_user' */
            'xf_user_profile' => array(
                'fake_dob_day' => 'TINYINT(3) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'fake_dob_day' */
                'fake_dob_month' => 'TINYINT(3) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'fake_dob_month' */
                'fake_dob_year' => 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT \'0\'', /* END 'fake_dob_year' */
            ), /* END 'xf_user_profile' */
            'xf_user_option' => array(
                'parent_field_locks' => 'MEDIUMBLOB NULL', /* END 'parent_field_locks' */
                'parent_revoked_permissions' => 'MEDIUMBLOB NULL', /* END 'parent_revoked_permissions' */
                'parent_limits' => 'MEDIUMBLOB NULL', /* END 'parent_limits' */
            ), /* END 'xf_user_option' */
        );
    } /* END _getTableChanges */

    protected function _getEnumValues()
    {
        return array(
            'xf_user_field' => array(
                'display_group' => array(
                    'add' => array(
                        'parental_control'
                    ), /* END 'add' */
                ), /* END 'display_group' */
            ), /* END 'xf_user_field' */
        );
    } /* END _getEnumValues */

    protected function _postInstall()
    {
        $this->_db->query(
            '
            INSERT IGNORE INTO xf_user_field
                (field_id, display_group, display_order, field_type, field_choices, match_type, match_regex, match_callback_class, match_callback_method, max_length, required, show_registration, user_editable, viewable_profile, viewable_message, display_template)
            VALUES
                (\'emergency_contact\', \'parental_control\', 1, \'textarea\', \'\', \'none\', \'\', \'\', \'\', 0, 0, 0, \'yes\', 1, 0, \'\')
        ');
        $addOn = $this->getModelFromCache('XenForo_Model_AddOn')->getAddOnById('YoYo_');
        if ($addOn) {
            $db->query("
                INSERT INTO xf_parent_authenticate (parent_email, parent_scheme_class, parent_data)
                    SELECT parent_email, parent_scheme_class, parent_data
                        FROM xf_parent_auwaindigoenticate"); 
            $db->query("
                UPDATE xf_user_profile
                    SET fake_dob_month=fake_dob_monwaindigo");
        }
    } /* END _postInstall */
}