<?php

class ThemeHouse_ParentalContro_Listener_LoadClassController extends ThemeHouse_Listener_LoadClass
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
            'XenForo_ControllerPublic_Account' => 'ThemeHouse_ParentalContro_Extend_XenForo_ControllerPublic_Account', /* END 'XenForo_ControllerPublic_Account' */
        );
    } /* END _getExtends */

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_ParentalContro' => array(
                'controller' => array(
                    'XenForo_ControllerPublic_Account'
                ), /* END 'controller' */
            ), /* END 'ThemeHouse_ParentalContro' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassController($class, array &$extend)
    {
        $loadClassController = new ThemeHouse_ParentalContro_Listener_LoadClassController($class, $extend, 'controller');
        $extend = $loadClassController->run();
    } /* END loadClassController */
}