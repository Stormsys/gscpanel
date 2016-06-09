<?php
using('PermissionsModel', 'App.Model');

/**
 * Models users on the system.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Model
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class UserModel
{
    private $_id = 0;
    private $_username;
    private $_password;
    private $_permissions;
    private $_first_name;
    private $_last_name;
    private $_email;

    public function __construct($id, $username, $password, $permissions, $firstname, $lastname, $email)
    {
        $this->_id = $id;
        $this->_username = $username;
        $this->_password = $password;
        $this->_permissions = new PermissionsModel($permissions);
        $this->_first_name = $firstname;
        $this->_last_name = $lastname;
        $this->_email = $email;
    }

    public function Permissions()
    {
        return $this->_permissions;
    }

    public function GetId()
    {
        return $this->_id;
    }

    public function GetUsername()
    {
        return $this->_username;
    }

    public function GetFirstName()
    {
        return $this->_first_name;
    }

    public function GetLastName()
    {
        return $this->_last_name;
    }

    public function GetEmail()
    {
        return $this->_email;
    }

    public function GetPassword()
    {
        return $this->_password;
    }

    public function SetPassword($password)
    {

    }

} 
