<?php

/**
 *
 * @see XenForo_Model_UserConfirmation
 */
class ThemeHouse_ParentalContro_Extend_XenForo_Model_UserConfirmation extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_Model_UserConfirmation
{
    /**
     * Send parent email confirmation to the specified user.
     *
     * @param array $user User to send to
     * @param array|null $confirmation Existing confirmation record; if null, generates a new record
     *
     * @return boolean True if the email was sent successfully
     */
    public function sendParentEmailConfirmation(array $user, array $confirmation = null)
    {
        if (!$confirmation) {
            $confirmation = $this->generateUserConfirmationRecord($user['user_id'], 'parent_email');
        }

        $params = array(
            'user' => $user,
            'confirmation' => $confirmation,
            'boardTitle' => XenForo_Application::get('options')->boardTitle
        );
        $mail = XenForo_Mail::create('th_parent_email_confirmation_parentalcontrol', $params, $user['language_id']);

        return $mail->send($user['parent_email'], (string) new XenForo_Phrase('th_parent_of_x_parentalcontrol', array('username' => $user['username'])));
    } /* END sendParentEmailConfirmation */

    /**
     * Sends parent a password reset request.
     *
     * @param array $user
     * @param array|null $confirmation If null, generates a new confirmation record
     *
     * @return boolean True if email sent successfully
     */
    public function sendParentPasswordResetRequest(array $user, array $confirmation = null)
    {
        if (!$confirmation) {
            $confirmation = $this->generateUserConfirmationRecord($user['user_id'], 'parent_password');
        }

        $params = array(
            'user' => $user,
            'confirmation' => $confirmation,
            'boardTitle' => XenForo_Application::get('options')->boardTitle
        );
        $mail = XenForo_Mail::create('th_lost_password_parentalcontrol', $params, $user['language_id']);

        return $mail->send($user['parent_email'], (string) new XenForo_Phrase('th_parent_of_x_parentalcontrol', array('username' => $user['username'])));
    } /* END sendParentPasswordResetRequest */

    /**
     * Send parent linked account confirmation to the specified user.
     *
     * @param array $user User to send to
     * @param array|null $confirmation Existing confirmation record; if null, generates a new record
     *
     * @return boolean True if the email was sent successfully
     */
    public function sendParentLinkConfirmation(array $user, array $confirmation = null)
    {
        if (!$confirmation) {
            $confirmation = $this->generateUserConfirmationRecord($user['user_id'], 'parent_email');
        }

        $params = array(
            'user' => $user,
            'confirmation' => $confirmation,
            'boardTitle' => XenForo_Application::get('options')->boardTitle
        );
        $mail = XenForo_Mail::create('th_user_link_confirmation_parentalcontrol', $params, $user['language_id']);

        return $mail->send($user['email'], $user['username']);
    } /* END sendParentLinkConfirmation */

    /**
     * Resets the specified user's parental control password and emails the password to the parent if requested.
     *
     * @param integer $userId
     * @param boolean $sendEmail
     *
     * @return string New password
     */
    public function resetParentPassword($userId, $sendEmail = true)
    {
        $dw = XenForo_DataWriter::create('XenForo_DataWriter_User');
        $dw->setExistingData($userId);

        $password = XenForo_Application::generateRandomString(8);

        $auth = XenForo_Authentication_Abstract::createDefault();
        $dw->set('parent_scheme_class', $auth->getClassName());
        $dw->set('parent_data', $auth->generate($password));
        $dw->save();

        $user = $dw->getMergedData();

        if ($sendEmail) {
            $params = array(
                'user' => $user,
                'password' => $password,
                'boardTitle' => XenForo_Application::get('options')->boardTitle,
                'boardUrl' => XenForo_Application::get('options')->boardUrl,
            );
            $mail = XenForo_Mail::create('th_lost_password_reset_parentalcontrol', $params, $user['language_id']);
            $mail->send($user['parent_email'], (string) new XenForo_Phrase('th_parent_of_x_parentalcontrol', array('username' => $user['username'])));
        }

        return $password;
    } /* END resetParentPassword */
}