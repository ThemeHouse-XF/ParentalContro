<?php

/**
 *
 * @see XenForo_ControllerPublic_Account
 */
class ThemeHouse_ParentalContro_Extend_XenForo_ControllerPublic_Account extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_ControllerPublic_Account
{

    /**
     *
     * @see XenForo_ControllerPublic_Account::actionPersonalDetails()
     */
    public function actionPersonalDetails()
    {
        $visitor = XenForo_Visitor::getInstance();

        $user = $visitor->toArray();

        $user['dob_day'] = $user['fake_dob_day'];
        $user['dob_month'] = $user['fake_dob_month'];
        $user['dob_year'] = $user['fake_dob_year'];

        /* @var $response XenForo_ControllerResponse_View */
        $response = parent::actionPersonalDetails();

        $response->subView->params['birthday'] = $this->_getUserProfileModel()->getUserBirthdayDetails($user, true);
        $response->subView->params['visitor'] = $user;

        if ($visitor['parent_field_locks']) {
            $parentFieldLocks = unserialize($visitor['parent_field_locks']);
        } else {
            $parentFieldLocks = array();
        }
        $isParentLoggedIn = $this->getModelFromCache('XenForo_Model_User')->isParentLoggedIn();

        if (!$isParentLoggedIn) {
            if (isset($parentFieldLocks['status'])) {
                $response->subView->params['canUpdateStatus'] = false;
            }
            if (isset($parentFieldLocks['avatar'])) {
                $response->subView->params['canEditAvatar'] = false;
            }
            if (isset($parentFieldLocks['custom_title'])) {
                $response->subView->params['canEditCustomTitle'] = false;
            }
        }

        return $response;
    } /* END actionPersonalDetails */

    /**
     *
     * @see XenForo_ControllerPublic_Account::actionPersonalDetailsSave()
     */
    public function actionPersonalDetailsSave()
    {
        $GLOBALS['XenForo_ControllerPublic_Account'] = $this;

        return parent::actionPersonalDetailsSave();
    } /* END actionPersonalDetailsSave */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionParentalControl()
    {
        $visitor = XenForo_Visitor::getInstance();

        if ($visitor['parental_control_state'] != 'enabled') {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control/register'));
        }

        if (!$this->_getUserModel()->isParentLoggedIn()) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control/login'));
        }

        $linkedUsers = $this->_getUserModel()->getUsers(
            array(
                'parent_email' => $visitor['parent_email']
            ));
        unset($linkedUsers[$visitor['user_id']]);

        $permissionModel = $this->getModelFromCache('XenForo_Model_Permission');
        $pairs = array();
        $xenOptions = XenForo_Application::get('options');
        foreach ($xenOptions->th_parentalControl_revocablePermissions as $permissionGroupId => $permissions) {
            foreach ($permissions as $permissionId => $permissionValue) {
                if ($permissionValue) {
                    $pairs[] = array(
                        $permissionGroupId,
                        $permissionId
                    );
                }
            }
        }
        foreach ($xenOptions->th_parentalControl_limits as $permissionGroupId => $permissions) {
            foreach ($permissions as $permissionId => $permissionValue) {
                if ($permissionValue) {
                    $pairs[] = array(
                        $permissionGroupId,
                        $permissionId
                    );
                }
            }
        }
        $interfaceGroups = $permissionModel->preparePermissionInterfaceGroups(
            $permissionModel->getAllPermissionInterfaceGroups());
        $permissionsGrouped = $permissionModel->preparePermissionsGrouped(
            $permissionModel->getPermissionsByPairs($pairs));
        $permissions = array();
        foreach ($permissionsGrouped as $permissionGroup) {
            foreach ($permissionGroup as $permission) {
                $permissions[] = $permission;
            }
        }

        $viewParams = array(
            'linkedUsers' => $linkedUsers,
            'birthday' => $this->_getUserProfileModel()->getUserBirthdayDetails($visitor->toArray(), true),

            'permissionInterfaceGroups' => $interfaceGroups,
            'permissionsInterfaceGrouped' => $permissionModel->getInterfaceGroupedPermissions($permissions,
                $interfaceGroups),
            'revokedPermissions' => ($visitor['parent_revoked_permissions'] ? unserialize(
                $visitor['parent_revoked_permissions']) : array()),
            'limits' => ($visitor['parent_limits'] ? unserialize($visitor['parent_limits']) : array()),

            'customFields' => $this->_getFieldModel()->prepareUserFields(
                $this->_getFieldModel()
                    ->getUserFields(
                    array(
                        'display_group' => 'parental_control'
                    ),
                    array(
                        'valueUserId' => XenForo_Visitor::getUserId()
                    )), true)
        );

        return $this->_getWrapper('account', 'parental-control',
            $this->responseView('ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl',
                'th_account_parentalcontrol', $viewParams));
    } /* END actionParentalControl */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionParentalControlSave()
    {
        $this->_assertPostOnly();

        $visitor = XenForo_Visitor::getInstance();

        if ($visitor['parental_control_state'] != 'enabled') {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control/register'));
        }

        $sessionId = XenForo_Session::getPublicSession(new Zend_Controller_Request_Http())->getSessionId();

        if (!isset($visitor['parent_session']) || !$visitor['parent_session'] || $visitor['parent_session'] != $sessionId) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control/login'));
        }

        $settings = $this->_input->filter(
            array(
                // preferences
                'dob_day' => XenForo_Input::UINT,
                'dob_month' => XenForo_Input::UINT,
                'dob_year' => XenForo_Input::UINT,

                // permissions
                'revoked_permissions' => XenForo_Input::ARRAY_SIMPLE,
                'revoked_permissions_shown' => XenForo_Input::ARRAY_SIMPLE,

                // limits
                'limits' => XenForo_Input::ARRAY_SIMPLE
            ));

        if ($visitor['dob_day'] && $visitor['dob_month'] && $visitor['dob_year']) {
            // can't change dob if set
            unset($settings['dob_day'], $settings['dob_month'], $settings['dob_year']);
        }

        $xenOptions = XenForo_Application::get('options');

        $revokedPermissions = array();
        foreach ($settings['revoked_permissions_shown'] as $permissionGroupId => $permissions) {
            foreach ($permissions as $permissionId) {
                if (!isset($settings['revoked_permissions'][$permissionGroupId][$permissionId]) &&
                     isset(
                        $xenOptions->th_parentalControl_revocablePermissions[$permissionGroupId][$permissionId])) {
                    $revokedPermissions[$permissionGroupId][$permissionId] = true;
                }
            }
        }
        unset($settings['revoked_permissions'], $settings['revoked_permissions_shown']);

        $limits = array();
        foreach ($settings['limits'] as $permissionGroupId => $permissions) {
            foreach ($permissions as $permissionId => $valueInt) {
                if ($valueInt >= 0 &&
                     isset($xenOptions->th_parentalControl_limits[$permissionGroupId][$permissionId])) {
                    $limits[$permissionGroupId][$permissionId] = $valueInt;
                }
            }
        }
        unset($settings['limits']);

        $customFields = $this->_input->filterSingle('custom_fields', XenForo_Input::ARRAY_SIMPLE);
        $customFieldsShown = $this->_input->filterSingle('custom_fields_shown', XenForo_Input::STRING,
            array(
                'array' => true
            ));

        $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
        $writer->setExistingData(XenForo_Visitor::getUserId());
        $writer->bulkSet($settings);
        $writer->setCustomFields($customFields, $customFieldsShown);
        $writer->revokeParentPermissions($revokedPermissions);
        $writer->setParentLimits($limits);
        $writer->preSave();

        if ($dwErrors = $writer->getErrors()) {
            return $this->responseError($dwErrors);
        }

        $writer->save();

        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildPublicLink('account/parental-control'));
    } /* END actionParentalControlSave */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionParentalControlLogin()
    {
        $visitor = XenForo_Visitor::getInstance();

        $sessionId = XenForo_Session::getPublicSession(new Zend_Controller_Request_Http())->getSessionId();

        if (isset($visitor['parent_session']) && $visitor['parent_session'] && $visitor['parent_session'] == $sessionId) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control'));
        }

        $viewParams = array();

        return $this->_getWrapper('account', 'parental-control',
            $this->responseView('ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl_Login',
                'th_account_login_parentalcontrol', $viewParams));
    } /* END actionParentalControlLogin */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionParentalControlLoginLogin()
    {
        $this->_assertPostOnly();

        $visitor = XenForo_Visitor::getInstance();

        $sessionId = XenForo_Session::getPublicSession(new Zend_Controller_Request_Http())->getSessionId();

        if (isset($visitor['parent_session']) && $visitor['parent_session'] && $visitor['parent_session'] == $sessionId) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control'));
        }

        $data = $this->_input->filter(
            array(
                'parent_email' => XenForo_Input::STRING,
                'parent_password' => XenForo_Input::STRING
            ));

        $userModel = $this->_getUserModel();

        $error = '';
        $authenticated = $userModel->validateParentAuthentication($data['parent_email'], $data['parent_password'],
            $error);
        if (!$authenticated) {
            return $this->responseError($error);
        }

        $session = XenForo_Application::getSession();

        /* @var $writer XenForo_DataWriter_User */
        $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
        $writer->setExistingData(XenForo_Visitor::getUserId());
        $writer->set('parent_session', $session->getSessionId());
        $writer->save();

        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildPublicLink('account/parental-control'),
            new XenForo_Phrase('th_login_successful_parentalcontrol'));
    } /* END actionParentalControlLoginLogin */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionParentalControlLogout()
    {
        $visitor = XenForo_Visitor::getInstance();

        $sessionId = XenForo_Session::getPublicSession(new Zend_Controller_Request_Http())->getSessionId();

        if (!isset($visitor['parent_session']) || !$visitor['parent_session'] || $visitor['parent_session'] != $sessionId) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control'));
        }

        if ($this->isConfirmedPost()) {
            /* @var $writer XenForo_DataWriter_User */
            $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
            $writer->setExistingData(XenForo_Visitor::getUserId());
            $writer->set('parent_session', '');
            $writer->save();

            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $this->getDynamicRedirect());
        }

        $viewParams = array();

        return $this->_getWrapper('account', 'parental-control',
            $this->responseView('ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl_Logout',
                'th_account_log_out_parentalcontrol', $viewParams));
    } /* END actionParentalControlLogout */

    public function actionParentalControlLostPassword()
    {
        $visitor = XenForo_Visitor::getInstance();

        $sessionId = XenForo_Session::getPublicSession(new Zend_Controller_Request_Http())->getSessionId();

        if (isset($visitor['parent_session']) && $visitor['parent_session'] && $visitor['parent_session'] == $sessionId) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control'));
        }

        $viewParams = array();

        return $this->_getWrapper('account', 'parental-control',
            $this->responseView('ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl_LostPassword',
                'th_account_lost_password_parentalcontrol', $viewParams));
    } /* END actionParentalControlLostPassword */

    public function actionParentalControlLostPasswordLost()
    {
        $this->_assertPostOnly();

        $visitor = XenForo_Visitor::getInstance();

        $sessionId = XenForo_Session::getPublicSession(new Zend_Controller_Request_Http())->getSessionId();

        if (isset($visitor['parent_session']) && $visitor['parent_session'] && $visitor['parent_session'] == $sessionId) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control'));
        }

        $confirmationModel = $this->_getUserConfirmationModel();

        $options = XenForo_Application::get('options');

        if ($options->lostPasswordTimeLimit) {
            $confirmation = $confirmationModel->getUserConfirmationRecord($visitor['user_id'], 'parent_password');
            if ($confirmation) {
                $timeDiff = XenForo_Application::$time - $confirmation['confirmation_date'];

                if ($options->lostPasswordTimeLimit > $timeDiff) {
                    $wait = $options->lostPasswordTimeLimit - $timeDiff;

                    return $this->responseError(
                        new XenForo_Phrase('must_wait_x_seconds_before_performing_this_action',
                            array(
                                'count' => $wait
                            )));
                }
            }
        }

        $confirmationModel->sendParentPasswordResetRequest($visitor->toArray());

        return $this->responseMessage(new XenForo_Phrase('password_reset_request_has_been_emailed_to_you'));
    } /* END actionParentalControlLostPasswordLost */

    /**
     * Confirms a lost password reset request and resets the password.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionParentalControlLostPasswordConfirm()
    {
        $confirmationModel = $this->_getUserConfirmationModel();

        $visitor = XenForo_Visitor::getInstance();

        $sessionId = XenForo_Session::getPublicSession(new Zend_Controller_Request_Http())->getSessionId();

        if (isset($visitor['parent_session']) && $visitor['parent_session'] && $visitor['parent_session'] == $sessionId) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control'));
        }

        $userId = XenForo_Visitor::getUserId();

        $confirmation = $confirmationModel->getUserConfirmationRecord($userId, 'parent_password');
        if (!$confirmation) {
            if (XenForo_Visitor::getUserId()) {
                return $this->responseError(new XenForo_Phrase('your_password_could_not_be_reset'));
            }
        }

        $confirmationKey = $this->_input->filterSingle('c', XenForo_Input::STRING);
        if ($confirmationKey) {
            $accountConfirmed = $confirmationModel->validateUserConfirmationRecord($confirmationKey, $confirmation);
        } else {
            $accountConfirmed = false;
        }

        if ($accountConfirmed) {
            $confirmationModel->resetParentPassword($userId);
            $confirmationModel->deleteUserConfirmationRecord($userId, 'password');
            return $this->responseMessage(new XenForo_Phrase('your_password_has_been_reset'));
        } else {
            return $this->responseError(new XenForo_Phrase('your_password_could_not_be_reset'));
        }
    } /* END actionParentalControlLostPasswordConfirm */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionParentalControlRegister()
    {
        $visitor = XenForo_Visitor::getInstance();

        if ($visitor['parental_control_state'] == 'enabled') {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control'));
        }

        $viewParams = array();

        return $this->_getWrapper('account', 'parental-control',
            $this->responseView('ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl_Register',
                'th_account_register_parentalcontrol', $viewParams));
    } /* END actionParentalControlRegister */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionParentalControlRegisterRegister()
    {
        $this->_assertPostOnly();

        $visitor = XenForo_Visitor::getInstance();

        if ($visitor['parental_control_state'] == 'enabled') {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control'));
        }

        $fields = $this->_input->filter(array(
            'parent_email' => XenForo_Input::STRING
        ));

        $passwords = $this->_input->filter(
            array(
                'parent_password' => XenForo_Input::STRING,
                'parent_password_confirm' => XenForo_Input::STRING
            ));

        $userModel = $this->_getUserModel();
        $record = $userModel->getParentAuthenticationRecordByEmail($fields['parent_email']);

        if ($record) {
            $error = '';
            $authenticated = $userModel->validateParentAuthentication($fields['parent_email'],
                $passwords['parent_password'], $error, false);
            if (!$authenticated) {
                return $this->responseError($error);
            }

            $session = XenForo_Application::getSession();

            /* @var $writer XenForo_DataWriter_User */
            $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
            $writer->setExistingData(XenForo_Visitor::getUserId());
            $writer->bulkSet($fields);
            $writer->set('parental_control_state', 'enabled');
            $writer->set('parent_session', $session->getSessionId());
            $writer->save();

            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('account/parental-control'),
                new XenForo_Phrase('th_parental_control_enabled_parentalcontrol'));
        } else {
            /* @var $writer XenForo_DataWriter_User */
            $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
            $writer->setExistingData(XenForo_Visitor::getUserId());
            $writer->bulkSet($fields);
            $writer->setParentPassword($passwords['parent_password'], $passwords['parent_password_confirm']);
            $writer->set('parental_control_state', 'email_confirm');
            $writer->save();

            $user = $writer->getMergedData();

            $this->_getUserConfirmationModel()->sendParentEmailConfirmation($user);

            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('account/parental-control'),
                new XenForo_Phrase('th_email_confirmation_has_been_sent_parentalcontrol'));
        }
    } /* END actionParentalControlRegisterRegister */

    public function actionParentalControlConfirmationEmail()
    {
        $userId = $this->_input->filterSingle('u', XenForo_Input::UINT);

        $confirmationModel = $this->_getUserConfirmationModel();

        $confirmation = $confirmationModel->getUserConfirmationRecord($userId, 'parent_email');
        if (!$confirmation) {
            return $this->responseError(
                new XenForo_Phrase('th_this_account_does_not_require_confirmation_parentalcontrol'));
        }

        $confirmationKey = $this->_input->filterSingle('c', XenForo_Input::STRING);
        if ($confirmationKey) {
            $accountConfirmed = $confirmationModel->validateUserConfirmationRecord($confirmationKey, $confirmation);
        } else {
            $accountConfirmed = false;
        }

        if ($accountConfirmed) {
            $dw = XenForo_DataWriter::create('XenForo_DataWriter_User');
            $dw->setExistingData($userId);
            $dw->set('parental_control_state', 'enabled');
            $dw->save();

            $confirmationModel->deleteUserConfirmationRecord($userId, 'parent_email');

            $user = $dw->getMergedData();

            $viewParams = array(
                'user' => $user
            );

            return $this->_getWrapper('account', 'parental-control',
                $this->responseView(
                    'ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl_ConfirmationEmail_Success',
                    'th_account_confirm_success_parentalcontrol', $viewParams));
        } else {
            return $this->responseError(
                new XenForo_Phrase('th_your_email_could_not_be_confirmed_parentalcontrol'));
        }
    } /* END actionParentalControlConfirmationEmail */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionParentalControlLink()
    {
        $this->_assertPostOnly();

        $visitor = XenForo_Visitor::getInstance();

        if ($visitor['parental_control_state'] != 'enabled') {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control'));
        }

        $fields = $this->_input->filter(array(
            'users' => XenForo_Input::STRING
        ));

        $usernames = array_filter(explode(',', $fields['users']));

        $userModel = $this->_getUserModel();
        $users = $userModel->getUsersByNames($usernames);

        foreach ($users as $userId => $user) {
            if ($user['parental_control_state'] == 'enabled' || !$user['email'] ||
                 $user['email'] == $visitor['parent_email']) {
                unset($users[$userId]);
                continue;
            }
            /* @var $writer XenForo_DataWriter_User */
            $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
            $writer->setExistingData($userId);
            $writer->set('parent_email', $visitor['parent_email']);
            $writer->set('parental_control_state', 'email_confirm');
            $writer->save();

            $user = $writer->getMergedData();

            $this->_getUserConfirmationModel()->sendParentLinkConfirmation($user);
        }

        if (count($users) == 1) {
            $successPhrase = new XenForo_Phrase('th_email_confirmation_has_been_sent_parentalcontrol');
        } elseif (count($users) > 1) {
            $successPhrase = new XenForo_Phrase('th_email_confirmations_have_been_sent_parentalcontrol');
        } else {
            return $this->responseError(new XenForo_Phrase('th_no_valid_users_found_parentalcontrol'));
        }

        if ($this->_noRedirect()) {
            $linkedUsers = $this->_getUserModel()->getUsers(
                array(
                    'parent_email' => $visitor['parent_email']
                ));
            unset($linkedUsers[$visitor['user_id']]);

            return $this->responseView('ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl_Link',
                'th_account_link_success_parentalcontrol',
                array(
                    'linkedUsers' => $linkedUsers
                ));
        }

        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildPublicLink('account/parental-control'), $successPhrase);
    } /* END actionParentalControlLink */

    /**
     * Resends the account confirmation if needed.
     *
     * @package XenForo_ControllerPublic_AccountConfirmation
     */
    public function actionParentalControlResend()
    {
        $visitor = XenForo_Visitor::getInstance();

        $userId = $this->_input->filterSingle('user_id', XenForo_Input::UINT);

        $user = $this->_getUserModel()->getUserById($userId);

        if (!$user) {
            $this->_responseError(new XenForo_Phrase('requested_user_not_found'));
        }

        if ($user['parent_email'] != $visitor['parent_email']) {
            return $this->responseNoPermission();
        }

        if ($user['parental_control_state'] == 'enabled') {
            return $this->responseError(
                new XenForo_Phrase('th_this_account_does_not_require_confirmation_parentalcontrol'));
        }

        if ($this->isConfirmedPost()) {
            $this->_getUserConfirmationModel()->sendParentLinkConfirmation($user);

            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('account/parental-control'),
                new XenForo_Phrase('th_email_confirmation_has_been_resent_parentalcontrol'));
        } else {
            $viewParams = array(
                'user' => $user
            );

            return $this->responseView('ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl_Resend',
                'th_account_confirm_resend_parentalcontrol', $viewParams);
        }
    } /* END actionParentalControlResend */

    /**
     *
     * @return XenForo_ControllerResponse_Redirect
     */
    public function actionParentalControlPasswordChange()
    {
        $this->_assertPostOnly();

        $visitor = XenForo_Visitor::getInstance();

        if ($visitor['parental_control_state'] != 'enabled') {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control'));
        }

        $input = $this->_input->filter(
            array(
                'old_parent_password' => XenForo_Input::STRING,
                'parent_password' => XenForo_Input::STRING,
                'parent_password_confirm' => XenForo_Input::STRING
            ));

        $auth = $this->_getUserModel()->getParentAuthenticationObjectByEmail($visitor['parent_email']);
        if (!$auth || !$auth->authenticate($visitor['parent_email'], $input['old_parent_password'])) {
            return $this->responseError(new XenForo_Phrase('your_existing_password_is_not_correct'));
        }

        $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
        $writer->setExistingData($visitor['user_id']);
        $writer->setParentPassword($input['parent_password'], $input['parent_password_confirm']);
        $writer->save();

        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildPublicLink('account/parental-control'));
    } /* END actionParentalControlPasswordChange */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionParentalControlDisable()
    {
        $visitor = XenForo_Visitor::getInstance();

        if ($visitor['parental_control_state'] != 'enabled') {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control/register'));
        }

        $userId = $this->_input->filterSingle('user_id', XenForo_Input::UINT);

        $user = $this->_getUserModel()->getUserById($userId);

        if (!$user) {
            $this->_responseError(new XenForo_Phrase('requested_user_not_found'));
        }

        if ($user['parental_control_state'] != 'enabled' || $user['parent_email'] != $visitor['parent_email']) {
            $this->_responseNoPermission();
        }

        if ($this->isConfirmedPost()) {
            /* @var $writer XenForo_DataWriter_User */
            $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
            $writer->setExistingData($user['user_id']);
            $writer->set('parental_control_state', 'disabled');
            $writer->save();

            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
                XenForo_Link::buildPublicLink('account/parental-control'),
                new XenForo_Phrase('th_parental_control_disabled_parentalcontrol'));
        } else {
            $viewParams = array(
                'user' => $user
            );

            return $this->responseView('ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl_Disable',
                'th_account_confirm_disable_parentalcontrol', $viewParams);
        }
    } /* END actionParentalControlDisable */

    public function actionParentalControlDownloadLogFile()
    {
        $GLOBALS['XenForo_ControllerPublic_Account'] = $this;

        $visitor = XenForo_Visitor::getInstance();

        $results = array();
        $users = $this->_getUserModel()->getUsers(
            array(
                'parent_email' => $visitor['parent_email']
            ));

        $constraints = array(
            'user' => array_keys($users),
            'date' => strtotime("-1 month", XenForo_Application::$time)
        );

        $searchModel = $this->getModelFromCache('XenForo_Model_Search');
        $searcher = new XenForo_Search_Searcher($searchModel);
        $results = $searcher->searchGeneral('', $constraints, 'date');

        $results = $this->getModelFromCache('XenForo_Model_Search')->getSearchResultsForDisplay($results);

        $viewParams = array(
            'results' => $results
        );

        $containerParams = array(
            'containerTemplate' => 'th_log_file_container_parentalcontrol'
        );

        return $this->responseView('ThemeHouse_ParentalContro_ViewPublic_Account_ParentalControl_LogFile',
            'th_log_file_parentalcontrol', $viewParams, $containerParams);
    } /* END actionParentalControlDownloadLogFile */

    /**
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionParentalControlToggleParentFieldLock()
    {
        $visitor = XenForo_Visitor::getInstance();

        if ($visitor['parental_control_state'] != 'enabled') {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL,
                XenForo_Link::buildPublicLink('account/parental-control'));
        }

        $fieldName = $this->_input->filterSingle('field_name', XenForo_Input::STRING);

        /* @var $writer XenForo_DataWriter_User */
        $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
        $writer->setExistingData($visitor['user_id']);
        if ($fieldName == 'status') {
            $writer->toggleParentRevokedPermission('profilePost', 'post');
        } else
            if ($fieldName == 'avatar') {
                $writer->toggleParentRevokedPermission('avatar', 'allowed');
            } else
                if ($fieldName == 'custom_title') {
                    $writer->toggleParentRevokedPermission('general', 'editCustomTitle');
                } else {
                    $writer->toggleParentFieldLock($fieldName);
                }
        $writer->save();

        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $this->getDynamicRedirect());
    } /* END actionParentalControlToggleParentFieldLock */

    /**
     *
     * @see XenForo_ControllerPublic_Account::_assertViewingPermissions()
     */
    protected function _assertViewingPermissions($action)
    {
        if (!in_array($action,
            array(
                'ParentalControl',
                'ParentalControlLogin',
                'ParentalControlLoginLogin',
                'ParentalControlLostPassword',
                'ParentalControlLostPasswordLost',
                'ParentalControlLostPasswordConfirm'
            ))) {
            return parent::_assertViewingPermissions($action);
        }
    } /* END _assertViewingPermissions */

    /**
     *
     * @return XenForo_Model_UserConfirmation
     */
    protected function _getUserConfirmationModel()
    {
        return $this->getModelFromCache('XenForo_Model_UserConfirmation');
    } /* END _getUserConfirmationModel */

    /**
     *
     * @return XenForo_Model_User
     */
    protected function _getUserModel()
    {
        return $this->getModelFromCache('XenForo_Model_User');
    } /* END _getUserModel */
}