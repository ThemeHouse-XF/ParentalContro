<?php

class ThemeHouse_ParentalContro_Listener_LoadClassDataWriter extends ThemeHouse_Listener_LoadClass
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
            'XenForo_DataWriter_User' => 'ThemeHouse_ParentalContro_Extend_XenForo_DataWriter_User', /* END 'XenForo_DataWriter_User' */
            'XenForo_DataWriter_UserField' => 'ThemeHouse_ParentalContro_Extend_XenForo_DataWriter_UserField', /* END 'XenForo_DataWriter_UserField' */
        );
    } /* END _getExtends */

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_ParentalContro' => array(
                'datawriter' => array(
                    'XenForo_DataWriter_User',
                    'XenForo_DataWriter_UserField'
                ), /* END 'datawriter' */
            ), /* END 'ThemeHouse_ParentalContro' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassDataWriter($class, array &$extend)
    {
        $loadClassDataWriter = new ThemeHouse_ParentalContro_Listener_LoadClassDataWriter($class, $extend, 'datawriter');
        $extend = $loadClassDataWriter->run();
    } /* END loadClassDataWriter */
}