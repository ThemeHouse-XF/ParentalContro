<?php

/**
 *
 * @see XenForo_Model_Thread
 */
class ThemeHouse_ParentalContro_Extend_XenForo_Model_Thread extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_Model_Thread
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