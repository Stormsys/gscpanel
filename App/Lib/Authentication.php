<?php
using('User', 'App.DAO');

/**
 * Provides common authentication functionality to the system.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Lib
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Authentication
{
    const PASSWORD_SALT = 'gscpanel';
    private $_user = null;

    public function __construct()
    {
        $userid = GSCP_Core()->Session()->Get('user');
        if ($userid != null && !empty($userid)) {
            $this->_SetCurrentUser(User::GetModelById($userid));
        }
    }

    public function __destruct()
    {
        GSCP_Core()->Session()->Save();
    }

    public function HashPassword($username, $password)
    {
        return md5(md5($username . self::PASSWORD_SALT) . md5($password . self::PASSWORD_SALT));
    }

    public function Logout()
    {
        $this->_SetCurrentUser(null);

        return $this->isLoggedIn();
    }

    public function Login($username, $password)
    {
        $this->_SetCurrentUser(User::GetModelByLogin($username, self::HashPassword($username, $password)));

        return $this->isLoggedIn();
    }

    public function isLoggedIn()
    {
        return !empty($this->_user);
    }

    public function GetUser()
    {
        return $this->_user;
    }

    private function _SetCurrentUser($user)
    {
        $this->_user = $user;


        if ($this->isLoggedIn())
            GSCP_Core()->Session()->Set('user', $this->_user->GetId());
        else
            GSCP_Core()->Session()->Set('user', null);

    }
}