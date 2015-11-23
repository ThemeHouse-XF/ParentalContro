<?php

class ThemeHouse_ParentalContro_Listener_LoadClassModel extends ThemeHouse_Listener_LoadClass
{

    /**
     * Gets the classes that are extended for this add-on.
     * See parent for explanation.
     *
     * @return array
     */
    protected function _getExtends()
    {
        return array(
            'XenForo_Model_Conversation' => 'ThemeHouse_ParentalContro_Extend_XenForo_Model_Conversation', /* END 'XenForo_Model_Conversation' */
            'XenForo_Model_Forum' => 'ThemeHouse_ParentalContro_Extend_XenForo_Model_Forum', /* END 'XenForo_Model_Forum' */
            'XenForo_Model_Permission' => 'ThemeHouse_ParentalContro_Extend_XenForo_Model_Permission', /* END 'XenForo_Model_Permission' */
            'XenForo_Model_PermissionCache' => 'ThemeHouse_ParentalContro_Extend_XenForo_Model_PermissionCache', /* END 'XenForo_Model_PermissionCache' */
            'XenForo_Model_Thread' => 'ThemeHouse_ParentalContro_Extend_XenForo_Model_Thread', /* END 'XenForo_Model_Thread' */
            'XenForo_Model_User' => 'ThemeHouse_ParentalContro_Extend_XenForo_Model_User', /* END 'XenForo_Model_User' */
            'XenForo_Model_UserConfirmation' => 'ThemeHouse_ParentalContro_Extend_XenForo_Model_UserConfirmation', /* END 'XenForo_Model_UserConfirmation' */
            'XenForo_Model_UserProfile' => 'ThemeHouse_ParentalContro_Extend_XenForo_Model_UserProfile', /* END 'XenForo_Model_UserProfile' */
            'XenForo_Model_UserField' => 'ThemeHouse_ParentalContro_Extend_XenForo_Model_UserField', /* END 'XenForo_Model_UserField' */
        );
    } /* END _getExtends */

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_ParentalContro' => array(
                'model' => array(
                    'XenForo_Model_Conversation',
                    'XenForo_Model_Forum',
                    'XenForo_Model_Permission',
                    'XenForo_Model_PermissionCache',
                    'XenForo_Model_Thread',
                    'XenForo_Model_User',
                    'XenForo_Model_UserConfirmation',
                    'XenForo_Model_UserProfile',
                    'XenForo_Model_UserField'
                ), /* END 'model' */
            ), /* END 'ThemeHouse_ParentalContro' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new ThemeHouse_ParentalContro_Listener_LoadClassModel($class, $extend, 'model');
        $extend = $loadClassModel->run();
    } /* END loadClassModel */
}