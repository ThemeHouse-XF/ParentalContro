<?php

/**
 *
 * @see XenForo_ViewPublic_Account_PersonalDetails
 */
class ThemeHouse_ParentalContro_Extend_XenForo_ViewPublic_Account_PersonalDetails extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_ViewPublic_Account_PersonalDetails
{

    public function renderHtml()
    {
        parent::renderHtml();

        $visitor = XenForo_Visitor::getInstance();

        $userModel = XenForo_Model::create('XenForo_Model_User');
        $parentFieldLocks = $userModel->getParentFieldLocks();

        if (isset($parentFieldLocks['about'])) {
            $bbCodeParser = new XenForo_BbCode_Parser(
                XenForo_BbCode_Formatter_Base::create('Base', array(
                    'view' => $this
                )));

            $about['message'] = $visitor['about'];
            $this->_params['aboutEditor'] = XenForo_ViewPublic_Helper_Message::getBbCodeWrapper($about, $bbCodeParser);
        }
    } /* END renderHtml */
}