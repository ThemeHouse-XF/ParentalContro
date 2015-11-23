<?php

/**
 *
 * @see XenForo_Model_Conversation
 */
class ThemeHouse_ParentalContro_Extend_XenForo_Model_Conversation extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_Model_Conversation
{

    /**
     *
     * @see XenForo_Model_Conversation::getConversationsSearchResultsForUserByIds()
     */
    public function getConversationsSearchResultsForUserByIds($userId, array $conversationIds)
    {
        if (isset($GLOBALS['XenForo_ControllerPublic_Account'])) {
            /* @var $controller XenForo_ControllerPublic_Account */
            $controller = $GLOBALS['XenForo_ControllerPublic_Account'];
            $action = $controller->getRouteMatch()->getAction();
            if ($action == 'parental-control/download-log-file') {
                return $this->getConversationsByIds($conversationIds);
            }
        }

        return parent::getConversationsSearchResultsForUserByIds($userId, $conversationIds);
    } /* END getConversationsSearchResultsForUserByIds */

    /**
     *
     * @see XenForo_Model_Conversation::getConversationMessagesSearchResultsForUserByIds()
     */
    public function getConversationMessagesSearchResultsForUserByIds($userId, array $conversationMessageIds)
    {
        if (isset($GLOBALS['XenForo_ControllerPublic_Account'])) {
            /* @var $controller XenForo_ControllerPublic_Account */
            $controller = $GLOBALS['XenForo_ControllerPublic_Account'];
            $action = $controller->getRouteMatch()->getAction();
            if ($action == 'parental-control/download-log-file') {
                return $this->getConversationMessagesByIds($conversationMessageIds);
            }
        }

        return parent::getConversationMessagesSearchResultsForUserByIds($userId, $conversationMessageIds);
    } /* END getConversationMessagesSearchResultsForUserByIds */

    /**
     *
     * @see XenForo_Model_Conversation::canViewConversation()
     */
    public function canViewConversation(array $conversation, &$errorPhraseKey = '', array $viewingUser = null)
    {
        if (isset($GLOBALS['XenForo_ControllerPublic_Account'])) {
            /* @var $controller XenForo_ControllerPublic_Account */
            $controller = $GLOBALS['XenForo_ControllerPublic_Account'];
            $action = $controller->getRouteMatch()->getAction();
            if ($action == 'parental-control/download-log-file') {
                return true;
            }
        }

        return parent::canViewConversation($conversation, $errorPhraseKey, $viewingUser);
    } /* END canViewConversation */
}