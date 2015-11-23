<?php

class ThemeHouse_ParentalContro_Extend_XenForo_Model_Permission extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_Model_Permission
{

    /**
     *
     * @param array $preparedOption
     * @return array $preparedOption
     */
    public function getParentalControlPreparedOption(array $preparedOption)
    {
        $permissionModel = $this->getModelFromCache('XenForo_Model_Permission');

        $interfaceGroups = $permissionModel->preparePermissionInterfaceGroups(
            $permissionModel->getAllPermissionInterfaceGroups());
        $permissions = $permissionModel->preparePermissions($permissionModel->getAllPermissions());

        $preparedOption['permissionInterfaceGroups'] = $interfaceGroups;
        $preparedOption['permissionsInterfaceGrouped'] = $permissionModel->getInterfaceGroupedPermissions($permissions,
            $interfaceGroups);

        return $preparedOption;
    } /* END getParentalControlPreparedOption */
}