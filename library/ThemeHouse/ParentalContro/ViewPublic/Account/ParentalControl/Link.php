<?php

class ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl_Link extends XenForo_ViewPublic_Base
{

    public function renderJson()
    {
        foreach ($this->_params['linkedUsers'] as $userId => &$user) {
            $user = $this->createTemplateObject('th_member_list_item_linked_parentalcontrol',
                array(
                    'user' => $user
                ));
        }

        return XenForo_ViewRenderer_Json::jsonEncodeForOutput(
            array(
                'users' => $this->_params['linkedUsers'],
                'userIds' => array_keys($this->_params['linkedUsers'])
            ));
    } /* END renderJson */
}