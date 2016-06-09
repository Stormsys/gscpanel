<?php
using('AjaxController', 'App.Lib');
using('User', 'App.DAO');
using('GameServerStatus', 'App.Model');
using('Authentication', 'App.Lib');

/**
 * Manages user functionality over ajax.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller.Api
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class UserController extends AjaxController
{
    private $_auth = null;

    public function __construct()
    {
        parent::__construct();
        $this->_auth = new Authentication();
    }


    /**
     * Allows the current user to update their credentials and information.
     */
    public function Update()
    {
        if ($this->_auth->isLoggedIn()) {
            try {
                if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email']) && !empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['email'])) {
                    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                        $this->Error('Invalid email address specified.');
                    } elseif (!preg_match("/^[-_a-zA-Z]{3,}$/", $_POST['firstname'])) {
                        $this->Error('Invalid First Name Specified.');
                    } elseif (!preg_match("/^[-_a-zA-Z]{3,}$/", $_POST['lastname'])) {
                        $this->Error('Invalid Last Name Specified.');
                    } else {
                        $firstname = $_POST['firstname'];
                        $lastname = $_POST['lastname'];
                        $email = $_POST['email'];

                        User::BasicUpdate($this->_auth->GetUser()->GetId(), $firstname, $lastname, $email);

                        if (isset($_POST['password']) && !empty($_POST['password'])) {
                            if (!preg_match("/^.{6,}$/", $_POST['password'])) {
                                $this->error('Password is too short!');
                            } else {
                                $password = $this->_auth->HashPassword($this->_auth->GetUser()->GetUsername(), $_POST['password']);
                                User::ChangePassword($this->_auth->GetUser()->GetId(), $password);
                            }
                        }
                        $this->out(null);
                    }
                } else {
                    $this->Error('You have provided invalid data.');
                }
            } catch (Exception $e) {
                $this->Error($e->getMessage());
            }
        } else
            $this->Error('You are not Logged In!');
    }


    /**
     * Logs the current user out of the system.
     */
    public function Logout()
    {
        $this->_auth->Logout();
        header('Location: /auth/login');
    }


    /**
     * Logs a user into the system using POST data.
     */
    public function Login()
    {
        try {
            if ($this->_auth->Login(strtolower($_POST['username']), $_POST['password']))
                $this->out(null);
            else
                $this->Error('Username not recognised or invalid password!');
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}