<?php

/**
 *
 * @see XenForo_DataWriter_User
 */
class ThemeHouse_ParentalContro_Extend_XenForo_DataWriter_User extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_DataWriter_User
{

    /**
     * If this is set, it represents a set of parent field locks to *replace*.
     * When it is null, no locks will be updated.
     * @var null array
     */
    protected $_parentFieldLocks = null;

    /**
     * If this is set, it represents a set of parent permission revocations to
     * *replace*.
     * When it is null, no revocations will be updated.
     * @var null array
     */
    protected $_parentRevokedPermissions = null;

    /**
     * If this is set, it represents a set of parent limits to *replace*.
     * When it is null, no revocations will be updated.
     * @var null array
     */
    protected $_parentLimits = null;

    /**
     *
     * @see XenForo_DataWriter_User::_getFields()
     */
    protected function _getFields()
    {
        $fields = parent::_getFields();

        $fields['xf_parent_authenticate']['parent_email'] = array(
            'type' => self::TYPE_STRING,
            'default' => array(
                'xf_user',
                'parent_email'
            )
        );
        $fields['xf_parent_authenticate']['parent_scheme_class'] = array(
            'type' => self::TYPE_STRING,
            'default' => 'XenForo_Authentication_NoPassword'
        );
        $fields['xf_parent_authenticate']['parent_data'] = array(
            'type' => self::TYPE_BINARY,
            'default' => ''
        );

        $fields['xf_user']['parent_email'] = array(
            'type' => self::TYPE_STRING,
            'maxLength' => 120,
            'verification' => array(
                '$this',
                '_verifyParentEmail'
            ),
            'default' => ''
        );
        $fields['xf_user']['parental_control_state'] = array(
            'type' => self::TYPE_STRING,
            'allowedValues' => array(
                'enabled',
                'email_confirm',
                'email_confirm_edit',
                'disabled'
            ),
            'default' => 'disabled'
        );
        $fields['xf_user']['parent_session'] = array(
            'type' => self::TYPE_STRING,
            'default' => ''
        );
        $fields['xf_user']['parent_session_start'] = array(
            'type' => self::TYPE_UINT,
            'default' => 0
        );

        $fields['xf_user_profile']['fake_dob_day'] = array(
            'type' => self::TYPE_UINT,
            'default' => 0,
            'max' => 31
        );
        $fields['xf_user_profile']['fake_dob_month'] = array(
            'type' => self::TYPE_UINT,
            'default' => 0,
            'max' => 12
        );
        $fields['xf_user_profile']['fake_dob_year'] = array(
            'type' => self::TYPE_UINT,
            'default' => 0,
            'max' => 2100
        );

        $fields['xf_user_option']['parent_field_locks'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );
        $fields['xf_user_option']['parent_revoked_permissions'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );
        $fields['xf_user_option']['parent_limits'] = array(
            'type' => self::TYPE_SERIALIZED,
            'default' => ''
        );

        return $fields;
    } /* END _getFields */

    /**
     *
     * @see XenForo_DataWriter_User::_getExistingData()
     */
    protected function _getExistingData($data)
    {
        $returnData = parent::_getExistingData($data);

        if ($returnData && isset($returnData['xf_user']['parent_email']) && $returnData['xf_user']['parent_email']) {
            $parentAuthenticate = $this->_getUserModel()->getParentAuthenticationRecordByEmail(
                $returnData['xf_user']['parent_email']);
            if ($parentAuthenticate) {
                $returnData['xf_parent_authenticate'] = $parentAuthenticate;
            }
        }

        return $returnData;
    } /* END _getExistingData */

    /**
     *
     * @see XenForo_DataWriter_User::_getUpdateCondition()
     */
    protected function _getUpdateCondition($tableName)
    {
        if ($tableName == 'xf_parent_authenticate') {
            return 'parent_email = ' . $this->_db->quote($this->get('parent_email'));
        }
        return parent::_getUpdateCondition($tableName);
    } /* END _getUpdateCondition */

    /**
     *
     * @see XenForo_DataWriter_User::_setAutoIncrementValue()
     */
    protected function _setAutoIncrementValue($insertId, $tableName, $updateAll = false)
    {
        if ($tableName == 'xf_parent_authenticate') {
            return true;
        }
        $success = parent::_setAutoIncrementValue($insertId, $tableName, $updateAll);
        if ($success && $updateAll) {
            if (isset($this->_newData['xf_parent_authenticate']['parent_email'])) {
                unset($this->_newData['xf_parent_authenticate']['user_id']);
            }
        }
        return $success;
    } /* END _setAutoIncrementValue */

    /**
     *
     * @see XenForo_DataWriter_User::_getTableList()
     */
    protected function _getTableList($tableName = '')
    {
        $tableList = parent::_getTableList($tableName);
        if (in_array('xf_parent_authenticate', $tableList) &&
             !isset($this->_newData['xf_parent_authenticate']['parent_email'])) {
            unset($tableList[array_search('xf_parent_authenticate', $tableList)]);
        }
        return $tableList;
    } /* END _getTableList */

    /**
     * Verification callback to check the parent email address is in a valid
     * form
     *
     * @param string Parent email Address
     *
     * @return bool
     */
    protected function _verifyParentEmail(&$email)
    {
        if ($this->isUpdate() && $email === $this->getExisting('parent_email')) {
            return true;
        }

        if (!$email) {
            return true;
        }

        if (!Zend_Validate::is($email, 'EmailAddress')) {
            $this->error(new XenForo_Phrase('please_enter_valid_email'), 'parent_email');
            return false;
        }

        if (XenForo_Helper_Email::isEmailBanned($email)) {
            $this->error(new XenForo_Phrase('email_address_you_entered_has_been_banned_by_administrator'),
                'parent_email');
            return false;
        }

        return true;
    } /* END _verifyParentEmail */

    /**
     * Sets the parent's password.
     *
     * @param string $password
     * @param string|false $passwordConfirm If a string, ensures that the
     * password and the confirm are the same
     *
     * @return boolean
     */
    public function setParentPassword($password, $passwordConfirm = false)
    {
        if ($passwordConfirm !== false && $password !== $passwordConfirm) {
            $this->error(new XenForo_Phrase('passwords_did_not_match'), 'password');
            return false;
        }

        $auth = XenForo_Authentication_Abstract::createDefault();
        $authData = $auth->generate($password);
        if (!$authData) {
            $this->error(new XenForo_Phrase('please_enter_valid_password'), 'password');
            return false;
        }

        $this->set('parent_scheme_class', $auth->getClassName());
        $this->set('parent_data', $authData);
        return true;
    } /* END setParentPassword */

    /**
     * Pre-save default setting.
     */
    protected function _preSaveDefaults()
    {
        parent::_preSaveDefaults();

        if (is_array($this->_parentFieldLocks)) {
            $this->set('parent_field_locks', serialize($this->_parentFieldLocks));
        }

        if (is_array($this->_parentRevokedPermissions)) {
            $this->set('parent_revoked_permissions', serialize($this->_parentRevokedPermissions));
        }

        if (is_array($this->_parentLimits)) {
            $this->set('parent_limits', serialize($this->_parentLimits));
        }
    } /* END _preSaveDefaults */

    /**
     * Pre-save handling
     */
    protected function _preSave()
    {
        $this->_checkLockedFields();

        parent::_preSave();

        $this->_enableParentalControl();
        $this->_updateParentalControlSessionStart();
    } /* END _preSave */

    protected function _enableParentalControl()
    {
        if ($this->isChanged('parental_control_state') && $this->get('parental_control_state') == 'disabled') {
            $this->set('parent_email', '');
            $this->set('parent_scheme_class', 'XenForo_Authentication_NoPassword');
            $this->set('parent_data', '');
        } else {
            if ($this->isChanged('parent_email')) {
                if ($this->get('email') && $this->get('parent_email') === $this->get('email')) {
                    $this->error(
                        new XenForo_Phrase('th_parent_email_address_must_not_be_same_as_account_parentalcontrol'),
                        'parent_email');
                }
                // TODO: delete old parent_authentication records?
            } else if ($this->isChanged('email') && $this->get('parent_email') &&
                 $this->get('email') === $this->get('parent_email')) {
                $this->error(
                    new XenForo_Phrase('th_parent_email_address_must_not_be_same_as_account_parentalcontrol'),
                    'email');
            }
            if ($this->get('parent_data') && $this->isChanged('parent_data')) {
                if ($this->get('parent_email') == '') {
                    $this->error(new XenForo_Phrase('please_enter_valid_email'), 'parent_email');
                } else {
                    $userModel = $this->_getUserModel();
                    $record = $userModel->getParentAuthenticationRecordByEmail($this->get('parent_email'));
                    if (!$record) {
                        $this->_db->query(
                            '
                            INSERT INTO xf_parent_authenticate (parent_email, parent_scheme_class, parent_data)
                            VALUES (?, ?, ?)
                        ',
                            array(
                                $this->get('parent_email'),
                                $this->get('parent_scheme_class'),
                                $this->get('parent_data')
                            ));
                    }
                }
            }
        }
    } /* END _enableParentalControl */

    protected function _updateParentalControlSessionStart()
    {
        if ($this->isChanged('parent_session')) {
            if ($this->get('parent_session')) {
                $this->set('parent_session_start', XenForo_Application::$time);
            } else {
                $this->set('parent_session_start', 0);
            }
        }
    } /* END _updateParentalControlSessionStart */

    protected function _checkLockedFields()
    {
        // TODO: might want to check this more thoroughly
        if (XenForo_Visitor::getUserId() != $this->get('user_id')) {
            return;
        }

        $parentFieldLocks = $this->_getUserModel()->getParentFieldLocks();

        foreach ($parentFieldLocks as $fieldName => $null) {
            switch ($fieldName) {
                case 'avatar':
                    if ($this->isChanged('avatar_date') || $this->isChanged('gravatar')) {
                        $this->error(new XenForo_Phrase('th_no_permission_to_edit_field_parentalcontrol'),
                            'avatar');
                    }
                    break;
                default:
                    if ($this->isChanged($fieldName)) {
                        $this->set($fieldName, $this->getExisting($fieldName));
                    }
                    break;
            }
        }
    } /* END _checkLockedFields */

    /**
     * Post-save handling
     */
    protected function _postSave()
    {
        $this->rebuildParentFieldLocks();
        $this->rebuildParentRevokedPermissions();
        $this->rebuildParentLimits();

        parent::_postSave();
    } /* END _postSave */

    /**
     *
     * @see XenForo_DataWriter_User::checkDob()
     */
    public function checkDob()
    {
        if (isset($GLOBALS['XenForo_ControllerPublic_Account'])) {
            /* @var $accountController XenForo_ControllerPublic_Account */
            $accountController = $GLOBALS['XenForo_ControllerPublic_Account'];

            if ($this->get('parental_control_state')) {
                if (!$this->getExisting('fake_dob_day') && !$this->getExisting('fake_dob_month') &&
                     !$this->getExisting('fake_dob_year')) {
                    if ($this->isChanged('dob_day') || $this->isChanged('dob_month') || $this->isChanged('dob_year')) {
                        $this->set('fake_dob_day', $this->get('dob_day'));
                        $this->set('fake_dob_month', $this->get('dob_month'));
                        $this->set('fake_dob_year', $this->get('dob_year'));
                    } else {
                        $input = $accountController->getInput()->filter(
                            array(
                                'dob_day' => XenForo_Input::UINT,
                                'dob_month' => XenForo_Input::UINT,
                                'dob_year' => XenForo_Input::UINT
                            ));
                        $this->set('fake_dob_day', $input['dob_day']);
                        $this->set('fake_dob_month', $input['dob_month']);
                        $this->set('fake_dob_year', $input['dob_year']);
                    }
                }

                // Parental control users cannot change their DOB, so always reset it
                $this->set('dob_day', $this->getExisting('dob_day'));
                $this->set('dob_month', $this->getExisting('dob_month'));
                $this->set('dob_year', $this->getExisting('dob_year'));

                return $this->checkFakeDob();
            }
        }

        return parent::checkDob();
    } /* END checkDob */

    /**
     * Checks that the fake date of birth entered is valid.
     * If not entered or not changed,
     * it's valid.
     *
     * @return boolean
     */
    public function checkFakeDob()
    {
        if ($this->isChanged('fake_dob_day') || $this->isChanged('fake_dob_month') || $this->isChanged('fake_dob_year')) {
            if (!$this->get('fake_dob_day') || !$this->get('fake_dob_month')) {
                $this->set('fake_dob_day', 0);
                $this->set('fake_dob_month', 0);
                $this->set('fake_dob_year', 0);
            } else {
                $year = $this->get('fake_dob_year');
                if (!$year) {
                    $year = 2008; // pick a leap year to be sure
                } else if ($year < 100) {
                    $year += ($year < 30 ? 2000 : 1900);
                    $this->set('fake_dob_year', $year);
                }

                if ($year > intval(date('Y')) || $year < 1900 ||
                     !checkdate($this->get('fake_dob_month'), $this->get('fake_dob_day'), $year) || gmmktime(0, 0, 0,
                        $this->get('fake_dob_month'), $this->get('fake_dob_day'), $year) >
                     XenForo_Application::$time + 86400)                 // +1 day to be careful with TZs ahead of GMT
{
                    if ($this->_importMode) {
                        // don't error, wipe it out
                        $this->set('fake_dob_day', 0);
                        $this->set('fake_dob_month', 0);
                        $this->set('fake_dob_year', 0);
                    } else {
                        $this->error(new XenForo_Phrase('please_enter_valid_date_of_birth'), 'dob');
                    }
                    return false;
                }
            }
        }

        return true;
    } /* END checkFakeDob */

    /**
     *
     * @param string $fieldName
     */
    public function toggleParentFieldLock($fieldName)
    {
        if (is_null($this->_parentFieldLocks)) {
            $this->_parentFieldLocks = ($this->get('parent_field_locks') ? unserialize($this->get('parent_field_locks')) : array());
        }

        if (isset($this->_parentFieldLocks[$fieldName])) {
            unset($this->_parentFieldLocks[$fieldName]);
        } else {
            $this->_parentFieldLocks[$fieldName] = true;
        }
    } /* END toggleParentFieldLock */

    public function rebuildParentFieldLocks()
    {
        $db = $this->_db;
        $userId = intval($this->get('user_id'));

        $db->delete('xf_parent_field_lock', 'user_id = ' . $db->quote($userId));

        $parentFieldLocks = ($this->get('parent_field_locks') ? unserialize($this->get('parent_field_locks')) : array());
        foreach ($parentFieldLocks as $fieldName => $null) {
            $db->query(
                '
                INSERT IGNORE INTO xf_parent_field_lock
                    (user_id, field_name)
                VALUES
                    (?, ?)
            ', array(
                    $userId,
                    $fieldName
                ));
        }
    } /* END rebuildParentFieldLocks */

    /**
     *
     * @param array $permissions
     */
    public function revokeParentPermissions(array $permissions)
    {
        $this->_parentRevokedPermissions = $permissions;
    } /* END revokeParentPermissions */

    /**
     *
     * @param array $limits
     */
    public function setParentLimits(array $limits)
    {
        $this->_parentLimits = $limits;
    } /* END setParentLimits */ /* END revokeParentPermissions */

    /**
     *
     * @param int $permissionGroupId
     * @param int $permissionId
     */
    public function toggleParentRevokedPermission($permissionGroupId, $permissionId)
    {
        if (is_null($this->_parentFieldLocks)) {
            $this->_parentRevokedPermissions = ($this->get('parent_revoked_permissions') ? unserialize(
                $this->get('parent_revoked_permissions')) : array());
        }

        if (isset($this->_parentRevokedPermissions[$permissionGroupId][$permissionId])) {
            unset($this->_parentRevokedPermissions[$permissionGroupId][$permissionId]);
        } else {
            $this->_parentRevokedPermissions[$permissionGroupId][$permissionId] = true;
        }
    } /* END toggleParentRevokedPermission */

    public function rebuildParentRevokedPermissions()
    {
        $db = $this->_db;
        $userId = intval($this->get('user_id'));

        $db->delete('xf_parent_revoked_permission', 'user_id = ' . $db->quote($userId));

        $parentRevokedPermissions = ($this->get('parent_revoked_permissions') ? unserialize(
            $this->get('parent_revoked_permissions')) : array());
        foreach ($parentRevokedPermissions as $permissionGroupId => $permissions) {
            foreach ($permissions as $permissionId => $null) {
                $db->query(
                    '
                    INSERT IGNORE INTO xf_parent_revoked_permission
                        (user_id, permission_group_id, permission_id)
                    VALUES
                        (?, ?, ?)
                ',
                    array(
                        $userId,
                        $permissionGroupId,
                        $permissionId
                    ));
            }
        }
    } /* END rebuildParentRevokedPermissions */

    public function rebuildParentLimits()
    {
        $db = $this->_db;
        $userId = intval($this->get('user_id'));

        $db->delete('xf_parent_limit', 'user_id = ' . $db->quote($userId));

        $parentLimits = ($this->get('parent_limits') ? unserialize(
            $this->get('parent_limits')) : array());
        foreach ($parentLimits as $permissionGroupId => $permissions) {
            foreach ($permissions as $permissionId => $valueInt) {
                $db->query(
                    '
                    INSERT IGNORE INTO xf_parent_limit
                        (user_id, permission_group_id, permission_id, value_int)
                    VALUES
                        (?, ?, ?, ?)
                ',
                    array(
                        $userId,
                        $permissionGroupId,
                        $permissionId,
                        $valueInt
                    ));
            }
        }
    } /* END rebuildParentLimits */
}