<?php

class ThemeHouse_ParentalContro_Listener_TemplatePostRender extends ThemeHouse_Listener_TemplatePostRender
{

    protected function _getTemplates()
    {
        return array(
            'account_personal_details',
            'account_signature',
            'account_contact_details',
            'account_privacy',
            'account_preferences',
            'PAGE_CONTAINER'
        );
    } /* END _getTemplates() */

    public static function templatePostRender($templateName, &$content, array &$containerData,
        XenForo_Template_Abstract $template)
    {
        $templatePostRender = new ThemeHouse_ParentalContro_Listener_TemplatePostRender($templateName, $content,
            $containerData, $template);
        list($content, $containerData) = $templatePostRender->run();
    } /* END templatePostRender */

    protected function _accountPersonalDetails()
    {
        $patterns = array();
        $visitor = XenForo_Visitor::getInstance();

        $viewParams = $this->_fetchViewParams();
        $userModel = $this->getModelFromCache('XenForo_Model_User');
        $parentFieldLocks = $userModel->getParentFieldLocks();
        $viewParams['isParentLoggedIn'] = $userModel->isParentLoggedIn();

        $fields = array(
            'location',
            'occupation',
            'homepage'
        );
        if ($viewParams['canUpdateStatus']) {
            $fields[] = 'status';
        }
        if ($viewParams['canEditCustomTitle']) {
            $fields[] = 'custom_title';
        }
        foreach ($viewParams['customFields'] as $customFieldId => $customField) {
            $fields[] = 'custom_field_' . $customFieldId;
        }
        foreach ($fields as $field) {
            $patterns[$field] = '#(<dl class="ctrlUnit">\s*<dt><label for="ctrl_' . $field .
                 '">[^>]*</label></dt>\s*<dd>)(.*</dd>\s*</dl>)#Us';
        }

        if ($viewParams['canEditAvatar']) {
            $patterns['avatar'] = '#(<dl class="ctrlUnit avatarEditor">\s*<dt><label>[^>]*</label></dt>\s*<dd>)(.*</dd>\s*</dl>)#Us';
        }
        $patterns['gender'] = '#(<dl class="ctrlUnit">\s*<dt><label>[^<]*</label></dt>\s*<dd>)(\s*<ul>\s*<li><label for="ctrl_gender.*</dd>\s*</dl>)#Us';
        $fields = array(
            'show_dob_date',
            'show_dob_year'
        );
        foreach ($fields as $field) {
            $patterns[$field] = '#(<label for="ctrl_' . $field . '">)(.*</label>)#Us';
        }
        $patterns['about'] = '#(<dl class="ctrlUnit OptOut">\s*<dt>\s*<label for="ctrl_about">[^>]*</label>\s*<dfn>[^>]*</dfn>\s*</dt>\s*<dd>)(.*</dd>\s*</dl>)#Us';

        foreach ($patterns as $fieldName => $pattern) {
            $viewParams['fieldName'] = $fieldName;
            $viewParams['locked'] = isset($parentFieldLocks[$fieldName]);
            if ($viewParams['isParentLoggedIn'] || $viewParams['locked']) {
                $replacement = '${1}' . $this->_render('th_lock_icon_parentalcontrol', $viewParams) . '${2}';
                $this->_patternReplace($pattern, $replacement);
            }
        }
    } /* END _accountPersonalDetails() */

    protected function _accountSignature()
    {
        $patterns = array();
        $visitor = XenForo_Visitor::getInstance();

        $viewParams = $this->_fetchViewParams();
        $userModel = $this->getModelFromCache('XenForo_Model_User');
        $parentFieldLocks = $userModel->getParentFieldLocks();
        $viewParams['isParentLoggedIn'] = $userModel->isParentLoggedIn();

        $patterns['signature'] = '#(<dl class="ctrlUnit fullWidth">\s*<dt></dt>\s*<dd>)(.*</dd>\s*</dl>)#Us';

        foreach ($patterns as $fieldName => $pattern) {
            $viewParams['fieldName'] = $fieldName;
            $viewParams['locked'] = isset($parentFieldLocks[$fieldName]);
            if ($viewParams['isParentLoggedIn'] || $viewParams['locked']) {
                $replacement = '${1}' . $this->_render('th_lock_icon_parentalcontrol', $viewParams) . '${2}';
                $this->_patternReplace($pattern, $replacement);
            }
        }
    } /* END _accountSignature() */

    protected function _accountContactDetails()
    {
        $patterns = array();
        $visitor = XenForo_Visitor::getInstance();

        $viewParams = $this->_fetchViewParams();
        $userModel = $this->getModelFromCache('XenForo_Model_User');
        $parentFieldLocks = $userModel->getParentFieldLocks();
        $viewParams['isParentLoggedIn'] = $userModel->isParentLoggedIn();

        $fields = array(
            'email'
        );
        foreach ($viewParams['customFields'] as $customFieldId => $customField) {
            $fields[] = 'custom_field_' . $customFieldId;
        }
        foreach ($fields as $field) {
            $patterns[$field] = '#(<dl class="ctrlUnit">\s*<dt>\s*<label for="ctrl_' . $field .
                 '">[^>]*</label>\s*</dt>\s*<dd>)(.*</dd>\s*</dl>)#Us';
        }

        $fields = array(
            'receive_admin_email',
            'allow_send_personal_conversation',
            'email_on_conversation'
        );
        foreach ($fields as $field) {
            $patterns[$field] = '#(<label for="ctrl_' . $field . '(?:_enable)?">)(.*</label>)#Us';
        }

        foreach ($patterns as $fieldName => $pattern) {
            $viewParams['fieldName'] = $fieldName;
            $viewParams['locked'] = isset($parentFieldLocks[$fieldName]);
            if ($viewParams['isParentLoggedIn'] || $viewParams['locked']) {
                $replacement = '${1}' . $this->_render('th_lock_icon_parentalcontrol', $viewParams) . '${2}';
                $this->_patternReplace($pattern, $replacement);
            }
        }
    } /* END _accountContactDetails() */

    protected function _accountPrivacy()
    {
        $patterns = array();
        $visitor = XenForo_Visitor::getInstance();

        $viewParams = $this->_fetchViewParams();
        $userModel = $this->getModelFromCache('XenForo_Model_User');
        $parentFieldLocks = $userModel->getParentFieldLocks();
        $viewParams['isParentLoggedIn'] = $userModel->isParentLoggedIn();

        $fields = array(
            'visible',
            'receive_admin_email',
            'show_dob_date',
            'show_dob_year',
            'allow_view_profile',
            'allow_post_profile',
            'allow_receive_news_feed',
            'allow_send_personal_conversation',
            'allow_view_identities'
        );
        foreach ($fields as $field) {
            $patterns[$field] = '#(<label for="ctrl_' . $field . '(?:_enable)?">)(.*</label>)#Us';
        }

        foreach ($patterns as $fieldName => $pattern) {
            $viewParams['fieldName'] = $fieldName;
            $viewParams['locked'] = isset($parentFieldLocks[$fieldName]);
            if ($viewParams['isParentLoggedIn'] || $viewParams['locked']) {
                $replacement = '${1}' . $this->_render('th_lock_icon_parentalcontrol', $viewParams) . '${2}';
                $this->_patternReplace($pattern, $replacement);
            }
        }
    } /* END _accountPrivacy() */

    protected function _accountPreferences()
    {
        $patterns = array();
        $visitor = XenForo_Visitor::getInstance();

        $viewParams = $this->_fetchViewParams();
        $userModel = $this->getModelFromCache('XenForo_Model_User');
        $parentFieldLocks = $userModel->getParentFieldLocks();
        $viewParams['isParentLoggedIn'] = $userModel->isParentLoggedIn();

        $fields = array(
            'style_id',
            'language_id'
        );
        foreach ($viewParams['customFields'] as $customFieldId => $customField) {
            $fields[] = 'custom_field_' . $customFieldId;
        }
        foreach ($fields as $field) {
            $patterns[$field] = '#(<dl class="ctrlUnit">\s*<dt>\s*<label for="ctrl_' . $field .
                 '">[^>]*</label>\s*</dt>\s*<dd>)(.*</dd>\s*</dl>)#Us';
        }

        $fields = array(
            'default_watch_state',
            'enable_rte',
            'content_show_signature',
            'restore_notices'
        );
        foreach ($fields as $field) {
            $patterns[$field] = '#(<label for="ctrl_' . $field . '(?:_enable)?">)(.*</label>)#Us';
        }

        $fields = array(
            'visible',
            'default_watch_state'
        );
        foreach ($fields as $field) {
            $patterns[$field] = '#(<label>)(\s*<input type="checkbox" name="' . $field . '".*</label>)#Us';
        }

        foreach ($patterns as $fieldName => $pattern) {
            $viewParams['fieldName'] = $fieldName;
            $viewParams['locked'] = isset($parentFieldLocks[$fieldName]);
            if ($viewParams['isParentLoggedIn'] || $viewParams['locked']) {
                $replacement = '${1}' . $this->_render('th_lock_icon_parentalcontrol', $viewParams) . '${2}';
                $this->_patternReplace($pattern, $replacement);
            }
        }
    } /* END _accountPreferences() */

    protected function _pageContainer()
    {
        $patterns = array();
        $visitor = XenForo_Visitor::getInstance();

        $userModel = $this->getModelFromCache('XenForo_Model_User');
        $parentFieldLocks = $userModel->getParentFieldLocks();

        if (isset($parentFieldLocks['visible'])) {
            $pattern = '#<li>\s*<form action="[^"]*" method="post" class="AutoValidator visibilityForm">.*</form>\s*</li>#Us';
            $this->_patternReplace($pattern);
        }
    } /* END _pageContainer() */
}