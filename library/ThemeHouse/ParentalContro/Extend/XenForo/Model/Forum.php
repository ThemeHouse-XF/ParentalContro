<?php

/**
 *
 * @see XenForo_Model_Forum
 */
class ThemeHouse_ParentalContro_Extend_XenForo_Model_Forum extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_Model_Forum
{

    public function standardizeViewingUserReferenceForNode($nodeId, array &$viewingUser = null,
        array &$nodePermissions = null)
    {
        parent::standardizeViewingUserReferenceForNode($nodeId, $viewingUser, $nodePermissions);

        if ($viewingUser['user_id'] && !$this->getModelFromCache('XenForo_Model_User')->isParentLoggedIn($viewingUser)) {
            $revokedPermissions = ($viewingUser['parent_revoked_permissions'] ? unserialize(
                $viewingUser['parent_revoked_permissions']) : array());

            if (isset($revokedPermissions['forum'])) {
                foreach ($revokedPermissions['forum'] as $permissionId => $null) {
                    unset($nodePermissions[$permissionId]);
                }
            }
        }
    } /* END standardizeViewingUserReferenceForNode */
}