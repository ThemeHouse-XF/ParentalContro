<?php

class ThemeHouse_ParentalContro_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_ParentalContro' => array(
                'controller' => array(
                    'XenForo_ControllerPublic_Account'
                ), /* END 'controller' */
                'datawriter' => array(
                    'XenForo_DataWriter_User',
                    'XenForo_DataWriter_UserField'
                ), /* END 'datawriter' */
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
                'view' => array(
                    'XenForo_ViewPublic_Account_PersonalDetails',
                    'XenForo_ViewPublic_Account_Signature'
                ), /* END 'view' */
            ), /* END 'ThemeHouse_ParentalContro' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassController($class, array &$extend)
    {
        $loadClassController = new ThemeHouse_ParentalContro_Listener_LoadClass($class, $extend, 'controller');
        $extend = $loadClassController->run();
    } /* END loadClassController */

    public static function loadClassDataWriter($class, array &$extend)
    {
        $loadClassDataWriter = new ThemeHouse_ParentalContro_Listener_LoadClass($class, $extend, 'datawriter');
        $extend = $loadClassDataWriter->run();
    } /* END loadClassDataWriter */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new ThemeHouse_ParentalContro_Listener_LoadClass($class, $extend, 'model');
        $extend = $loadClassModel->run();
    } /* END loadClassModel */

    public static function loadClassView($class, array &$extend)
    {
        $loadClassView = new ThemeHouse_ParentalContro_Listener_LoadClass($class, $extend, 'view');
        $extend = $loadClassView->run();
    } /* END loadClassView */
}