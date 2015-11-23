<?php

/**
 *
 * @see XenForo_ViewPublic_Account_Signature
 */
class ThemeHouse_ParentalContro_Extend_XenForo_ViewPublic_Account_Signature extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_ViewPublic_Account_Signature
{

    public function renderHtml()
    {
        parent::renderHtml();

        $visitor = XenForo_Visitor::getInstance();

        $userModel = XenForo_Model::create('XenForo_Model_User');
        $parentFieldLocks = $userModel->getParentFieldLocks();

        if (isset($parentFieldLocks['signature'])) {
            $bbCodeParser = new XenForo_BbCode_Parser(
                XenForo_BbCode_Formatter_Base::create('Base', array(
                    'view' => $this
                )));

            $signature['message'] = $visitor['signature'] . "\n";
            $this->_params['signatureEditor'] = XenForo_ViewPublic_Helper_Message::getBbCodeWrapper($signature,
                $bbCodeParser);
        }
    } /* END renderHtml */
}