<?php

/**
 *
 * @see XenForo_Model_UserField
 */
class ThemeHouse_ParentalContro_Extend_XenForo_Model_UserField extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_Model_UserField
{

    /**
     * Gets the possible user field groups.
     * Used to display in form in ACP.
     *
     * @return array [group] => keys: value, label, hint (optional)
     */
    public function getUserFieldGroups()
    {
        $userFieldGroups = parent::getUserFieldGroups();

        $userFieldGroups['parental_control'] = array(
            'value' => 'parental_control',
            'label' => new XenForo_Phrase('th_parental_control_parentalcontrol'),
            'hint' => new XenForo_Phrase('these_fields_will_never_be_displayed_on_users_profile')
        );

        return $userFieldGroups;
    } /* END getUserFieldGroups */
}