<?php
using('GameServer', 'App.DAO');
using('AjaxController', 'App.Lib');
using('Authentication', 'App.Lib');

/**
 * Provides FTP in brwoser functionality to users.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller.Api
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class FtpController extends AjaxController
{
    private $auth;

    /**
     * validates that a user is currently logged in before constructing.
     */
    public function __construct()
    {
        parent::__construct();
        $this->auth = new Authentication();
        if (!$this->auth->isLoggedIn()) {
            header('location: /auth/login');

            die();
        }

        header('PAGE_STATE: file-manager');
    }

    /**
     * Internally validate that a user has access to the game sever specified, otherwise output a json error.
     *
     * @param int $gsid the id of the game server.
     */
    private function _ValidateServerPermissions($gsid)
    {
        try {
            if (!($this->auth->GetUser()->Permissions()->Has('admin') || GameServer::GetModelById($gsid)->GetOwnerId() == $this->auth->GetUser()->GetId())) {
                $this->Error('Invalid Permissions');
                die();
            }
        } catch (Exception $e) {
            $this->Error($e->getMessage());
            die();
        }
    }

    /**
     * provides an ajax api to update files using POST.
     *
     * @param int $gsid the id of the game server.
     */
    public function Edit($gsid)
    {
        $this->_ValidateServerPermissions($gsid);
        try {
            if (isset($_POST['data'])) {
                $new_data = $_POST['data'];

                $dir = '';
                if (isset($_GET['path'])) {
                    $dir = $_GET['path'];
                }
                $gameserver = GameServer::GetModelById($gsid);

                //generate the ftp:// uri
                $link = "ftp://{$gameserver->GetFtpUser()}:{$gameserver->GetFtpPass()}@{$gameserver->GetIp()}:21/{$dir}";

                //allow the file to be overwritten.
                $options = array('ftp' => array('overwrite' => true));
                $stream = stream_context_create($options);

                //get file contents
                $content = file_put_contents($link, $new_data, 0, $stream);

                $this->Display('default/ftp/edit', array(
                    'cur_path' => $dir,
                    'content' => $content
                ));
                $this->out(null);
            } else {
                $this->Error('No value set.');
            }
        } catch (Exception $e) {
            $this->Error($e->getMessage());
        }
    }
}