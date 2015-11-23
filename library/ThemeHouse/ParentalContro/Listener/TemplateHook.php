<?php

class ThemeHouse_ParentalContro_Listener_TemplateHook extends ThemeHouse_Listener_TemplateHook
{

    protected function _getHooks()
    {
        return array(
            'account_wrapper_sidebar_settings',
            'navigation_visitor_tab_links1',
            'body'
        );
    } /* END _getHooks */

    public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
    {
        $templateHook = new ThemeHouse_ParentalContro_Listener_TemplateHook($hookName, $contents, $hookParams, $template);
        $contents = $templateHook->run();
    } /* END templateHook */

    protected function _accountWrapperSidebarSettings()
    {
        $pattern = '#<li><a\s*class="[^"]*"\s*href="' . preg_quote(XenForo_Link::buildPublicLink('account/privacy')) .
             '">[^<]*</a></li>#s';
        $this->_appendTemplateAtPattern($pattern, 'th_account_wrapper_sidebar_parentalcontrol');

        $externalAuthPages = array(
            'facebook',
            'twitter',
            'vk',
            'google',
            'steam'
        );
        foreach ($externalAuthPages as $externalAuthPage) {
            $pattern = '#<li><a\s*class="[^"]*"\s*href="' .
                 preg_quote(XenForo_Link::buildPublicLink('account/' . $externalAuthPage)) . '">[^>]*</a></li>#s';
            $this->_patternReplace($pattern, '');
        }
    } /* END _accountWrapperSidebarSettings */ /* END _accountWrapperSidebar */

    protected function _navigationVisitorTabLinks1()
    {
        $pattern = '#<li><a href="' . preg_quote(XenForo_Link::buildPublicLink('account/privacy')) . '">[^<]*</a></li>#s';
        $this->_appendTemplateAtPattern($pattern, 'th_navigation_visitor_tab_parentalcontrol');
    }

    protected function _body()
    {
        $visitor = XenForo_Visitor::getInstance();
        $sessionId = XenForo_Application::getSession()->getSessionId();
        if (isset($visitor['parent_session']) && $visitor['parent_session'] && $visitor['parent_session'] == $sessionId) {
            $pattern = '#<fieldset id="moderatorBar">\s*<div class="pageWidth">\s*<div class="pageContent">#Us';
            if (!preg_match($pattern, $this->_contents)) {
                $this->_prependTemplate('moderator_bar');
            }
            $this->_appendTemplateAtPattern($pattern, 'th_moderator_bar_logged_in_parentalcontrol');
        }
    } /* END _body */
}