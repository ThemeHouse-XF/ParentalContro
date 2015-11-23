<?php

class ThemeHouse_ParentalContro_Listener_LoadClassView extends ThemeHouse_Listener_LoadClass
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
            'XenForo_ViewPublic_Account_PersonalDetails' => 'ThemeHouse_ParentalContro_Extend_XenForo_ViewPublic_Account_PersonalDetails', /* END 'XenForo_ViewPublic_Account_PersonalDetails' */
            'XenForo_ViewPublic_Account_Signature' => 'ThemeHouse_ParentalContro_Extend_XenForo_ViewPublic_Account_Signature', /* END 'XenForo_ViewPublic_Account_Signature' */
        );
    } /* END _getExtends */

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_ParentalContro' => array(
                'view' => array(
                    'XenForo_ViewPublic_Account_PersonalDetails',
                    'XenForo_ViewPublic_Account_Signature'
                ), /* END 'view' */
            ), /* END 'ThemeHouse_ParentalContro' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassView($class, array &$extend)
    {
        $loadClassView = new ThemeHouse_ParentalContro_Listener_LoadClassView($class, $extend, 'view');
        $extend = $loadClassView->run();
    } /* END loadClassView */
}