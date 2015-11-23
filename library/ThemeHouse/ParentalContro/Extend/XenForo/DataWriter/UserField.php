<?php

/**
 *
 * @see XenForo_DataWriter_UserField
 */
class ThemeHouse_ParentalContro_Extend_XenForo_DataWriter_UserField extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_DataWriter_UserField
{

    /**
     *
     * @see XenForo_DataWriter_UserField::_getFields()
     */
    protected function _getFields()
    {
        $fields = parent::_getFields();

        $fields['xf_user_field']['display_group']['allowedValues'][] = 'parental_control';

        return $fields;
    } /* END _getFields */
}