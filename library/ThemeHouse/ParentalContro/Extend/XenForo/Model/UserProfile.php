<?php

/**
 *
 * @see XenForo_Model_UserProfile
 */
class ThemeHouse_ParentalContro_Extend_XenForo_Model_UserProfile extends XFCP_ThemeHouse_ParentalContro_Extend_XenForo_Model_UserProfile
{

    /**
     *
     * @see XenForo_Model_UserProfile::getUserBirthdayDetails()
     */
    public function getUserBirthdayDetails(array $user, $force = false)
    {
        if ($force || $user['parental_control_state'] != 'enabled') {
            return parent::getUserBirthdayDetails($user, $force);
        }
        if ($user['fake_dob_day'] && ($force || $user['show_dob_date'])) {
            if ($user['fake_dob_year'] && ($force || $user['show_dob_year'])) {
                return array(
                    'age' => $this->getUserAge($user, $force),
                    'timeStamp' => new DateTime("$user[fake_dob_year]-$user[fake_dob_month]-$user[fake_dob_day]"),
                    'format' => 'absolute'
                );
            } else {
                return array(
                    'age' => false,
                    'timeStamp' => new DateTime("2000-$user[fake_dob_month]-$user[fake_dob_day]"),
                    'format' => 'monthDay'
                );
            }
        } else {
            return false;
        }
    } /* END getUserBirthdayDetails */

    /**
     *
     * @see XenForo_Model_UserProfile::getUserAge()
     */
    public function getUserAge(array $user, $force = false)
    {
        if ($force || $user['parental_control_state'] != 'enabled') {
            return parent::getUserAge($user, $force);
        }
        if ($user['fake_dob_year'] && ($force || ($user['show_dob_date'] && $user['show_dob_year']))) {
            return $this->calculateAge($user['fake_dob_year'], $user['fake_dob_month'], $user['fake_dob_day']);
        } else {
            return false;
        }
    } /* END getUserAge */
}