<?php
using('PageController', 'App.Lib');
using('GameServer', 'App.DAO');
using('GameServerTemplate', 'App.DAO');
using('QueueController', 'App.Controller');
using('SSH', 'Core');
using('DedicatedServer', 'App.DAO');
using('Authentication', 'App.Lib');
using('Queue', 'App.DAO');
using('QueueEvent', 'App.Model');

/**
 * Handles pages that are used for the administration section also contains CRUD methods.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class AdminController extends PageController
{
    private $_auth;

    public function __construct()
    {
        parent::__construct();
        $this->_auth = new Authentication();

        if (!$this->_auth->isLoggedIn() || !($this->_auth->GetUser()->Permissions()->Has('admin'))) {
            header('Location: /dashboard');
            die();
        }

        header('PAGE_STATE: admin');
    }

    /**
     * Displays a list of game servers on the system.
     */
    public function Invoke()
    {
        $this->Display('admin/gameservers', array(
            'servers' => GameServer::GetServers(1, 1)->fetchAll()
        ));
    }

    /**
     * Deletes a game server from the system.
     *
     * @param int $gsid the game servers id.
     */
    public function GameserverDelete($gsid)
    {
        if ($gsid != 'new') {
            if (!GameServer::GetById($gsid)->fetch()) {
                header('Location: /admin/');
                die();
            }
        }
        GameServer::SetServerStatus($gsid, GameServerStatus::PENDING);
        Queue::AddAction($gsid, QueueEvent::DELETE_SERVER);
        $this->Display('admin/gameserver_done');
    }

    /**
     * Edits game server details.
     *
     * @param int $gsid the game servers id.
     */
    public function GameserverEdit($gsid)
    {
        if ($gsid != 'new') {
            if (!GameServer::GetById($gsid)->fetch()) {
                header('Location: /admin/gameserver-edit/new');
                die();
            }
        }
        $error = false;
        $submit = false;
        $errormsg = '';
        if (isset($_POST['submit'])) {
            $submit = true;
            if (isset($_POST['nickname'])
                && isset($_POST['ds_id'])
                && isset($_POST['template_id'])
                && isset($_POST['owner-id'])
                && isset($_POST['ip'])
                && isset($_POST['port'])
                && isset($_POST['slot_count'])
                && isset($_POST['paramater_overide'])
            ) {
                if (empty($_POST['nickname'])) {
                    $error = true;
                    $errormsg .= 'Please provide a nickname for the Game Server.<br/>';
                }
                if (!filter_var($_POST['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $error = true;
                    $errormsg .= 'The IP provided is not a valid IPV4 address.<br/>';
                }
                if (!filter_var($_POST['port'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 65535)))) {
                    $error = true;
                    $errormsg .= 'The Port provided is not a valid IPV4 port.<br/>';
                }
                if (empty($_POST['template_id']) || !GameServerTemplate::GetModelById($_POST['template_id'])) {
                    $error = true;
                    $errormsg .= 'The Template provided is not valid.<br/>';
                } else {
                    $template = GameServerTemplate::GetModelById($_POST['template_id']);
                    if (!filter_var($_POST['slot_count'], FILTER_VALIDATE_INT, array("options" => array("min_range" => $template->GetMinSlots(), "max_range" => $template->GetMaxSlots())))) {
                        $error = true;
                        $errormsg .= "The Slots are not in the allow range({$template->GetMinSlots()}-{$template->GetMaxSlots()}).<br/>";
                    }
                }
                if (empty($_POST['ds_id']) || !DedicatedServer::GetModelById($_POST['ds_id'])) {
                    $error = true;
                    $errormsg .= 'The Dedicated Server provided is not valid.<br/>';
                }
                if (empty($_POST['owner-id']) || !User::GetModelById($_POST['owner-id'])) {
                    $error = true;
                    $errormsg .= 'The User provided is not valid.<br/>';
                }
                if (!$error) {
                    if (isset($gsid) && $gsid != 'new') {

                        if (isset($_POST['ftp_password'])) {
                            try {
                                if (!preg_match("/^.{6,}$/", $_POST['ftp_password'])) {
                                    $error = true;
                                    $errormsg .= 'FTP Password is too short!<br/>';
                                } else {
                                    $original = GameServer::GetModelById($gsid);
                                    GameServer::Update($gsid, $_POST['ds_id'], $_POST['template_id'], $_POST['owner-id'], $_POST['nickname'], $_POST['ip'], $_POST['port'], $_POST['slot_count'],
                                        $_POST['paramater_overide'], $_POST['ftp_password']);
                                    if ($original->GetFtpPass() != $_POST['ftp_password']) {
                                        Queue::AddAction($gsid, QueueEvent::UPDATE_FTP_PASSWORD);
                                    }
                                }
                            } catch (Exception $e) {
                                $error = true;
                                $errormsg = $e->getMessage();
                            }
                        }
                    } else {
                        try {
                            $gsid = GameServer::Insert($_POST['ds_id'], $_POST['template_id'], $_POST['owner-id'], $_POST['nickname'], $_POST['ip'], $_POST['port'], $_POST['slot_count'],
                                $_POST['paramater_overide'], '', '', '');

                            GameServer::SoftUpdate($gsid, '/home/gscp_' . $gsid, 'gscp_' . $gsid, $this->GeneratePassword());
                            GameServer::SetServerStatus($gsid, GameServerStatus::PENDING);

                            Queue::AddAction($gsid, QueueEvent::INSTALL_SERVER);

                            header('Location: /admin/gameserver-edit/' . $gsid);
                            die();
                        } catch (Exception $e) {
                            $error = true;
                            $errormsg = $e->getMessage();
                        }
                    }
                }
            } else {
                $error = true;
                $errormsg = 'There was an error in the data submitted, please try again.';
            }
        }

        $this->Display('admin/gameserver_edit', array(
            'gs' => (isset($gsid) && $gsid != 'new') ? GameServer::GetModelById($gsid) : null,
            'dservers' => DedicatedServer::GetAllNameIds()->fetchAll(),
            'templates' => GameServerTemplate::GetAllNameIds()->fetchAll(),
            'users' => User::GetAllNameIds()->fetchAll(),
            'post' => $_POST,
            'error' => $error,
            'submit' => $submit,
            'errormsg' => $errormsg
        ));
    }

    /**
     * Generates a random password.
     */
    private function GeneratePassword()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars), 0, rand(8, 15));
    }

    /**
     * Edits a dedicated servers information.
     *
     * @param int $dsid the dedicated servers id.
     */
    public function DedicatedEdit($dsid)
    {
        if ($dsid != 'new') {
            if (!DedicatedServer::GetById($dsid)->fetch()) {
                header('Location: /admin/dedicated-edit/new');
                die();
            }
        }
        $error = false;
        $submit = false;
        $errormsg = '';
        if (isset($_POST['submit'])) {
            $submit = true;
            if (isset($_POST['main_ip']) && isset($_POST['nickname']) && isset($_POST['ssh_ip']) && isset($_POST['ssh_port']) && isset($_POST['ssh_username']) && isset($_POST['ssh_password']) && isset($_POST['installs_dir'])) {
                if (!filter_var($_POST['main_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $error = true;
                    $errormsg .= 'The Main IP provided is not a valid IPV4 address.<br/>';
                }
                if (!filter_var($_POST['ssh_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $error = true;
                    $errormsg .= 'The SSH IP provided is not a valid IPV4 address.<br/>';
                }
                if (!filter_var($_POST['ssh_port'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 65535)))) {
                    $error = true;
                    $errormsg .= 'The SSH Port provided is not a valid IPV4 port.<br/>';
                }
                if (empty($_POST['nickname'])) {
                    $error = true;
                    $errormsg .= 'Please provide a nickname for the server.<br/>';
                }
                if (!$error) {
                    if (isset($dsid) && $dsid != 'new') {
                        try {
                            DedicatedServer::Update($dsid, $_POST['main_ip'], $_POST['nickname'], $_POST['ssh_ip'], $_POST['ssh_port'], $_POST['ssh_username'], $_POST['ssh_password'], $_POST['installs_dir']);
                        } catch (Exception $e) {
                            $error = true;
                            $errormsg = $e->getMessage();
                        }
                    } else {
                        try {
                            $id = DedicatedServer::Insert($_POST['main_ip'], $_POST['nickname'], $_POST['ssh_ip'], $_POST['ssh_port'], $_POST['ssh_username'], $_POST['ssh_password'], $_POST['installs_dir']);
                            header('Location: /admin/dedicated-edit/' . $id);
                            die();
                        } catch (Exception $e) {
                            $error = true;
                            $errormsg = $e->getMessage();
                        }
                    }
                }
            } else {
                $error = true;
                $errormsg = 'There was an error in the data submitted, please try again.';
            }
        }
        $this->Display('admin/dedicated_edit', array(
            'server' => (isset($dsid) && $dsid != 'new') ? DedicatedServer::GetModelById($dsid) : null,
            'post' => $_POST,
            'error' => $error,
            'submit' => $submit,
            'errormsg' => $errormsg
        ));
    }


    /**
     * Deletes a dedeicated server on the system.
     *
     * @param int $dsid the dedicated servers id.
     */
    public function DedicatedDelete($dsid)
    {
        DedicatedServer::Delete($dsid);
        header('Location: /admin/dedicated/');
        die();
    }


    /**
     * Lists all dedicated servers on the system.
     */
    public function Dedicated()
    {
        $this->Display('admin/dediservers', array(
            'servers' => DedicatedServer::GetAll()->fetchAll()
        ));
    }

    /**
     * Lists all templates on the system.
     */
    public function Template()
    {
        $this->Display('admin/templates', array(
            'templates' => GameServerTemplate::GetAll()->fetchAll()
        ));
    }


    /**
     * Edits a template information.
     *
     * @param int $id id for the template.
     */
    public function TemplateEdit($id)
    {
        if ($id != 'new') {
            if (!GameServerTemplate::GetById($id)->fetch()) {
                header('Location: /admin/template-edit/new');
                die();
            }
        }
        $error = false;
        $submit = false;
        $errormsg = '';
        if (isset($_POST['submit'])) {
            $submit = true;
            if (isset($_POST['long_name']) && isset($_POST['min_slots']) && isset($_POST['max_slots']) && isset($_POST['default_slots']) && isset($_POST['default_port']) && isset($_POST['default_cmd']) && isset($_POST['game_files_zip']) && isset($_POST['connection_url'])) {

                if (!filter_var($_POST['min_slots'], FILTER_VALIDATE_INT, array("min_range" => 1, "max_range" => 256))) {
                    $error = true;
                    $errormsg .= 'The minimum slots provided are incorrect.<br/>';
                }
                if (!filter_var($_POST['max_slots'], FILTER_VALIDATE_INT, array("options" => array("min_range" => $_POST['min_slots'], "max_range" => 256)))) {
                    $error = true;
                    $errormsg .= 'The maximum slots provided are incorrect.<br/>';
                }
                if (!filter_var($_POST['default_slots'], FILTER_VALIDATE_INT, array("options" => array("min_range" => $_POST['min_slots'], "max_range" => $_POST['max_slots'])))) {
                    $error = true;
                    $errormsg .= 'The default slots provided are incorrect.<br/>';
                }
                if (!filter_var($_POST['default_port'], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => 65535)))) {
                    $error = true;
                    $errormsg .= 'The default port provided is not a valid IPV4 port.<br/>';
                }
                if (!$error) {
                    if (isset($id) && $id != 'new') {
                        try {

                            if (GameServerTemplate::GetModelById($id) != false) {
                                GameServerTemplate::Update($id, $_POST['long_name'], $_POST['min_slots'], $_POST['max_slots'], $_POST['default_slots'], $_POST['default_port'], $_POST['default_cmd'], $_POST['game_files_zip'], $_POST['connection_url']);
                            } else {
                                $error = true;
                                $errormsg = 'attempting to modify an invalid template.';
                            }
                        } catch (Exception $e) {
                            $error = true;
                            $errormsg = $e->getMessage();
                        }
                    } else {
                        try {
                            $id = GameServerTemplate::Insert($_POST['long_name'], $_POST['min_slots'], $_POST['max_slots'], $_POST['default_slots'], $_POST['default_port'], $_POST['default_cmd'], $_POST['game_files_zip'], $_POST['connection_url']);

                            header('Location: /admin/template-edit/' . $id);
                            die();
                        } catch (Exception $e) {
                            $error = true;
                            $errormsg = $e->getMessage();
                        }
                    }
                }
            } else {
                $error = true;
                $errormsg = 'There was an error in the data submitted, please try again.';
            }
        }

        $this->Display('admin/template_edit', array(
            'template' => (isset($id) && $id != 'new') ? GameServerTemplate::GetModelById($id) : null,
            'post' => $_POST,
            'error' => $error,
            'submit' => $submit,
            'errormsg' => $errormsg
        ));
    }


    /**
     * Deletes a template from the system.
     *
     * @param int $id id for the template.
     */
    public function TemplateDelete($id)
    {
        GameServerTemplate::Delete($id);
        header('Location: /admin/template/');
        die();
    }

    /**
     * Lists users on the system.
     */
    public function User()
    {
        $this->Display('admin/users', array(
            'users' => User::GetAll()->fetchAll()
        ));
    }


    /**
     * Edits a users information.
     *
     * @param int $id the users id.
     */
    public function UserEdit($id)
    {
        if ($id != 'new') {
            if (!User::GetById($id)->fetch()) {
                header('Location: /admin/user-edit/new');
                die();
            }
        }
        $error = false;
        $submit = false;
        $errormsg = '';
        if (isset($_POST['submit'])) {
            $submit = true;
            if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['username']) && isset($_POST['email']) && !empty($_POST['firstname']) && !empty($_POST['username']) && !empty($_POST['lastname']) && !empty($_POST['email'])) {
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $error = true;
                    $errormsg .= 'Invalid email address specified.<br/>';
                }
                if (!preg_match("/^[-_a-zA-Z]{3,}$/", $_POST['firstname'])) {
                    $error = true;
                    $errormsg .= 'Invalid First Name Specified.<br/>';
                }
                if (!preg_match("/^[-_a-zA-Z]{3,}$/", $_POST['lastname'])) {
                    $error = true;
                    $errormsg .= 'Invalid Last Name Specified.<br/>';
                }
                if (!preg_match("/^[-_a-zA-Z0-9]{3,}$/", $_POST['username'])) {
                    $error = true;
                    $errormsg .= 'Invalid Username Specified.<br/>';
                }
                if (!$error) {
                    if (isset($id) && $id != 'new') {
                        $firstname = $_POST['firstname'];
                        $lastname = $_POST['lastname'];
                        $email = $_POST['email'];
                        $username = strtolower($_POST['username']);
                        try {
                            User::BasicUpdateAdmin($id, $username, $firstname, $lastname, $email);
                            if (isset($_POST['password']) && !empty($_POST['password'])) {
                                if (!preg_match("/^.{6,}$/", $_POST['password'])) {
                                    $error = true;
                                    $errormsg .= 'Password is too short!<br/>';
                                } else {
                                    $password = $this->_auth->HashPassword($username, $_POST['password']);
                                    User::ChangePassword($id, $password);
                                }
                            }
                        } catch (Exception $e) {
                            $error = true;
                            $errormsg = $e->getMessage();
                        }
                    } elseif (isset($_POST['password']) && !empty($_POST['password'])) {
                        try {
                            $firstname = $_POST['firstname'];
                            $lastname = $_POST['lastname'];
                            $email = $_POST['email'];
                            $username = $_POST['username'];
                            $password = $this->_auth->HashPassword($username, $_POST['password']);

                            $id = User::Insert($username, $firstname, $lastname, $email, $password);

                            header('Location: /admin/user-edit/' . $id);
                            die();
                        } catch (Exception $e) {
                            $error = true;
                            $errormsg = $e->getMessage();
                        }
                    } else {
                        $error = true;
                        $errormsg .= 'Password not specified!<br/>';
                    }
                }
            } else {
                $error = true;
                $errormsg = 'There was an error in the data submitted, please try again.';
            }

        }

        $this->Display('admin/user_edit', array(
            'user' => (isset($id) && $id != 'new') ? User::GetModelById($id) : null,
            'post' => $_POST,
            'error' => $error,
            'submit' => $submit,
            'errormsg' => $errormsg
        ));
    }


    /**
     * Deletes a user from the system.
     *
     * @param int $id the userss id.
     */
    public function UserDelete($id)
    {
        User::Delete($id);
        header('Location: /admin/user/');
        die();
    }

}