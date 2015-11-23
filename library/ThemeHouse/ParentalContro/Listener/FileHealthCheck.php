<?php

class ThemeHouse_ParentalContro_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/ThemeHouse/ParentalContro/CronEntry/ExpireSessions.php' => '74a86a474012f64a4c8ed65d592347e1',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/ControllerPublic/Account.php' => '4daaf471f26112dc832a86057f3ea74d',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/DataWriter/User.php' => 'd7bf807dd271f0927c5fa13a10d6b97b',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/DataWriter/UserField.php' => '6c4d95590e41cc80797b332e43d24cb8',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/Model/Conversation.php' => '57fa0748e2d56676911f645003e8bb44',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/Model/Forum.php' => '151dbc289b0eebfe9bec6b347997b895',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/Model/Permission.php' => 'fc94c97aabc62c8a856c225e0d82faa6',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/Model/PermissionCache.php' => '0209983156dc18f3107abce66bfc330f',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/Model/Thread.php' => '2bc5620a6ef57089adb64c8c5365f630',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/Model/User.php' => '85a5e011ead184313b4838e34de97962',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/Model/UserConfirmation.php' => '4872e3e4fd9a9ed2b37cd5d3011dce95',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/Model/UserField.php' => '43e0317ce8c661886218e19c78b045a8',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/Model/UserProfile.php' => 'cefc6ccd721c9994820f60ea4397d18e',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/ViewPublic/Account/PersonalDetails.php' => '080c81ebff451d5ad8a62b33c3db10ae',
                'library/ThemeHouse/ParentalContro/Extend/XenForo/ViewPublic/Account/Signature.php' => '1918307b121676605a59e031e3416074',
                'library/ThemeHouse/ParentalContro/Install/Controller.php' => 'cdab750279d587f2cfc759cbeb781110',
                'library/ThemeHouse/ParentalContro/Listener/LoadClass.php' => '9331f9ae3014bc61058d62b0872d231f',
                'library/ThemeHouse/ParentalContro/Listener/LoadClassController.php' => '6d95412a59cfbe5a9ea3c00040f15b6c',
                'library/ThemeHouse/ParentalContro/Listener/LoadClassDataWriter.php' => '3cf855739b89b9e00e8a10148d8d94a1',
                'library/ThemeHouse/ParentalContro/Listener/LoadClassModel.php' => 'bc51137d8f2a91a1dc5c01ae03d046f3',
                'library/ThemeHouse/ParentalContro/Listener/LoadClassView.php' => '16db3b63db756b2300dfe54345664110',
                'library/ThemeHouse/ParentalContro/Listener/TemplateHook.php' => 'e09f6de65f2f39e10f9c166c03214daa',
                'library/ThemeHouse/ParentalContro/Listener/TemplatePostRender.php' => 'f26bde8fd592a3db40d36fca2d382923',
                'library/ThemeHouse/ParentalContro/Option/Limits.php' => '24eade010489030a8439bc828067db63',
                'library/ThemeHouse/ParentalContro/Option/Permissions.php' => '82e647f1f231de0fac6ad4cb4ed67e80',
                'library/ThemeHouse/ParentalContro/ViewPublic/Account/ParentalControl/Link.php' => '4b99af362df0e80a9e818663114e8966',
                'library/ThemeHouse/ParentalContro/ViewPublic/Account/ParentalControl/LogFile.php' => 'ade834f9ad1c343d52f4e2efb7300847',
                'library/ThemeHouse/Install.php' => '18f1441e00e3742460174ab197bec0b7',
                'library/ThemeHouse/Install/20151109.php' => '2e3f16d685652ea2fa82ba11b69204f4',
                'library/ThemeHouse/Deferred.php' => 'ebab3e432fe2f42520de0e36f7f45d88',
                'library/ThemeHouse/Deferred/20150106.php' => 'a311d9aa6f9a0412eeba878417ba7ede',
                'library/ThemeHouse/Listener/ControllerPreDispatch.php' => 'fdebb2d5347398d3974a6f27eb11a3cd',
                'library/ThemeHouse/Listener/ControllerPreDispatch/20150911.php' => 'f2aadc0bd188ad127e363f417b4d23a9',
                'library/ThemeHouse/Listener/InitDependencies.php' => '8f59aaa8ffe56231c4aa47cf2c65f2b0',
                'library/ThemeHouse/Listener/InitDependencies/20150212.php' => 'f04c9dc8fa289895c06c1bcba5d27293',
                'library/ThemeHouse/Listener/LoadClass.php' => '5cad77e1862641ddc2dd693b1aa68a50',
                'library/ThemeHouse/Listener/LoadClass/20150518.php' => 'f4d0d30ba5e5dc51cda07141c39939e3',
                'library/ThemeHouse/Listener/Template.php' => '0aa5e8aabb255d39cf01d671f9df0091',
                'library/ThemeHouse/Listener/Template/20150106.php' => '8d42b3b2d856af9e33b69a2ce1034442',
                'library/ThemeHouse/Listener/TemplateHook.php' => 'a767a03baad0ca958d19577200262d50',
                'library/ThemeHouse/Listener/TemplateHook/20150106.php' => '71c539920a651eef3106e19504048756',
                'library/ThemeHouse/Listener/TemplatePostRender.php' => 'b6da98a55074e4cde833abf576bc7b5d',
                'library/ThemeHouse/Listener/TemplatePostRender/20150106.php' => 'efccbb2b2340656d1776af01c25d9382',
            ));
    }
}