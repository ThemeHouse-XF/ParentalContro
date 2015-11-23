<?php

class ThemeHouse_ParentalContro_Option_Limits
{

    /**
     * Renders checkboxes allowing the selection of limits.
     *
     * @param XenForo_View $view View object
     * @param string $fieldPrefix Prefix for the HTML form field name
     * @param array $preparedOption Prepared option info
     * @param boolean $canEdit True if an "edit" link should appear
     *
     * @return XenForo_Template_Abstract Template object
     */
    public static function renderOption(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit)
    {
        $permissionModel = XenForo_Model::create('XenForo_Model_Permission');

        $preparedOption = $permissionModel->getParentalControlPreparedOption($preparedOption);
        return XenForo_ViewAdmin_Helper_Option::renderOptionTemplateInternal(
            'th_option_limits_parentalcontrol', $view, $fieldPrefix, $preparedOption, $canEdit);
    } /* END renderOption */
}