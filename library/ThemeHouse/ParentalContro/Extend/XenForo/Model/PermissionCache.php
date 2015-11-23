<?php

/**
 *
 * @see XenForo_Model_PermissionCache
 */
class ThemeHouse_ParentalContro_Extend_XenForo_Model_PermissionCache extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_Model_PermissionCache
{

    /**
     *
     * @see XenForo_Model_PermissionCache::getContentPermissionsForItem()
     */
    public function getContentPermissionsForItem($permissionCombinationId, $contentType, $contentId)
    {
        $contentPermissions = parent::getContentPermissionsForItem($permissionCombinationId, $contentType, $contentId);

        if (!$this->getModelFromCache('XenForo_Model_User')->isParentLoggedIn()) {
            $userInfo = XenForo_Visitor::getInstance();

            $revokedPermissions = (!empty($userInfo['parent_revoked_permissions']) ? unserialize(
                $userInfo['parent_revoked_permissions']) : array());

            foreach ($revokedPermissions as $permissionGroupId => $permissions) {
                foreach ($permissions as $permissionId => $null) {
                    unset($contentPermissions[$permissionGroupId][$permissionId]);
                }
            }

            $limits = (!empty($userInfo['parent_limits']) ? unserialize($userInfo['parent_limits']) : array());

            foreach ($limits as $permissionGroupId => $permissions) {
                foreach ($permissions as $permissionId => $valueInt) {
                    if (isset($contentPermissions[$permissionGroupId][$permissionId]) && ($contentPermissions[$permissionGroupId][$permissionId] ==
                         -1 || $contentPermissions[$permissionGroupId][$permissionId] > $valueInt)) {
                        $contentPermissions[$permissionGroupId][$permissionId] = $valueInt;
                    }
                }
            }
        }

        return $contentPermissions;
    } /* END getContentPermissionsForItem */
}