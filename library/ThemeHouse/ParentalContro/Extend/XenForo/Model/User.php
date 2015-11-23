<?php

/**
 *
 * @see XenForo_Model_User
 */
class ThemeHouse_ParentalContro_Extend_XenForo_Model_User extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_Model_User
{

    /**
     *
     * @see XenForo_Model_User::prepareUserConditions()
     */
    public function prepareUserConditions(array $conditions, array &$fetchOptions)
    {
        $db = $this->_getDb();
        $sqlConditions = array(
            parent::prepareUserConditions($conditions, $fetchOptions)
        );

        if (!empty($conditions['parent_email'])) {
            if (is_array($conditions['parent_email'])) {
                $sqlConditions[] = 'user.parent_email IN (' . $db->quote($conditions['parent_email']) . ')';
            } else {
                $sqlConditions[] = 'user.parent_email = ' . $db->quote($conditions['parent_email']);
            }
        }

        return $this->getConditionsForClause($sqlConditions);
    } /* END prepareUserConditions */

    /**
     * Gets the user authentication record by user ID.
     *
     * @param string $email
     *
     * @return array false
     */
    public function getParentAuthenticationRecordByEmail($email)
    {
        return $this->_getDb()->fetchRow(
            '

            SELECT *
            FROM xf_parent_authenticate
            WHERE parent_email = ?

        ', $email);
    } /* END getParentAuthenticationRecordByEmail */

    /**
     * Returns an auth object based on an input userid
     *
     * @param integer Userid
     *
     * @return XenForo_Authentication_Abstract false
     */
    public function getParentAuthenticationObjectByEmail($email)
    {
        $authenticate = $this->getParentAuthenticationRecordByEmail($email);
        if (!$authenticate) {
            return false;
        }

        $auth = XenForo_Authentication_Abstract::create($authenticate['parent_scheme_class']);
        if (!$auth) {
            return false;
        }

        $auth->setData($authenticate['parent_data']);
        return $auth;
    } /* END getParentAuthenticationObjectByEmail */

    /**
     * Logs the given parent in.
     * Exceptions are thrown on errors.
     *
     * @param string $email Parent email address
     * @param string $password
     * @param string $error Error string (by ref)
     *
     * @return boolean true if successful, false otherwise
     */
    public function validateParentAuthentication($email, $password, &$error = '', $checkVisitor = true)
    {
        $visitor = XenForo_Visitor::getInstance();

        if ($checkVisitor && $email != $visitor['parent_email']) {
            $error = new XenForo_Phrase('th_email_address_is_incorrect_parental_control');
            return false;
        }

        $authentication = $this->getParentAuthenticationObjectByEmail($email);
        if (!$authentication || !$authentication->authenticate($visitor['user_id'], $password)) {
            $error = new XenForo_Phrase('incorrect_password');
            return false;
        }

        return true;
    } /* END validateParentAuthentication */

    public function isParentLoggedIn($viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);

        $sessionId = XenForo_Session::getPublicSession(new Zend_Controller_Request_Http())->getSessionId();

        if (!isset($viewingUser['parent_session']) || !$viewingUser['parent_session'] ||
             $viewingUser['parent_session'] != $sessionId) {
            return false;
        }

        return true;
    } /* END isParentLoggedIn */

    public function getParentFieldLocks($viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);

        if (isset($viewingUser['parent_field_locks']) && $viewingUser['parent_field_locks']) {
            $parentFieldLocks = unserialize($viewingUser['parent_field_locks']);
        } else {
            $parentFieldLocks = array();
        }

        if (!isset($viewingUser['parent_revoked_permissions']) || !$viewingUser['parent_revoked_permissions']) {
            return array();
        } else {
            $revokedPermissions = unserialize($viewingUser['parent_revoked_permissions']);
        }
        if (!XenForo_Permission::hasPermission($viewingUser['permissions'], 'avatar', 'allowed') ||
             isset($revokedPermissions['avatar']['allowed'])) {
            $parentFieldLocks['avatar'] = true;
        }

        if (!XenForo_Permission::hasPermission($viewingUser['permissions'], 'general', 'editCustomTitle') ||
             isset($revokedPermissions['general']['editCustomTitle'])) {
            $parentFieldLocks['custom_title'] = true;
        }

        if (!XenForo_Permission::hasPermission($viewingUser['permissions'], 'profilePost', 'post') ||
             isset($revokedPermissions['profilePost']['post'])) {
            $parentFieldLocks['status'] = true;
        }

        return $parentFieldLocks;
    } /* END getParentFieldLocks */

    /**
     * Gets the visiting user's information based on their user ID.
     *
     * @param integer $userId
     *
     * @return array
     */
    public function getVisitingUserById($userId)
    {
        $userInfo = parent::getVisitingUserById($userId);

        if ($userInfo['user_id'] && !$this->isParentLoggedIn($userInfo)) {
            $userPermissions = unserialize($userInfo['global_permission_cache']);

            $revokedPermissions = ($userInfo['parent_revoked_permissions'] ? unserialize(
                $userInfo['parent_revoked_permissions']) : array());

            foreach ($revokedPermissions as $permissionGroupId => $permissions) {
                foreach ($permissions as $permissionId => $null) {
                    unset($userPermissions[$permissionGroupId][$permissionId]);
                }
            }

            $limits = ($userInfo['parent_limits'] ? unserialize($userInfo['parent_limits']) : array());

            foreach ($limits as $permissionGroupId => $permissions) {
                foreach ($permissions as $permissionId => $valueInt) {
                    if (isset($userPermissions[$permissionGroupId][$permissionId]) &&
                         ($userPermissions[$permissionGroupId][$permissionId] == -1 ||
                         $userPermissions[$permissionGroupId][$permissionId] > $valueInt)) {
                        $userPermissions[$permissionGroupId][$permissionId] = $valueInt;
                    }
                }
            }

            $userInfo['global_permission_cache'] = serialize($userPermissions);
        }

        return $userInfo;
    } /* END getVisitingUserById */
}