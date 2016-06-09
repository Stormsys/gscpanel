<?php
using('GameServer', 'App.DAO');
using('Authentication', 'App.Lib');

/**
 * Renders page for browsing files via ftp.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class FtpController extends Controller
{
    private $auth;

    public function __construct()
    {
        $this->auth = new Authentication();
        if (!$this->auth->isLoggedIn()) {
            header('location: /auth/login');

            die();
        }

        header('PAGE_STATE: file-manager');
    }


    /**
     * Validates permissions.
     *
     * @param int $gsid the id of the game server.
     */
    private function _ValidateServerPermissions($gsid)
    {
        try {
            if (!($this->auth->GetUser()->Permissions()->Has('admin') || GameServer::GetModelById($gsid)->GetOwnerId() == $this->auth->GetUser()->GetId())) {
                throw new Exception('invalid permission');
            }
        } catch (Exception $e) {
            throw new Exception('invalid permission');
        }
    }

    /**
     * Opens a file for reading in a text area.
     *
     * @param int $gsid the id of the game server.
     */
    public function View($gsid)
    {
        $this->_ValidateServerPermissions($gsid);
        $dir = '';
        if (isset($_GET['path'])) {
            $dir = $_GET['path'];
        }
        $gameserver = GameServer::GetModelById($gsid);


        $link = "ftp://{$gameserver->GetFtpUser()}:{$gameserver->GetFtpPass()}@{$gameserver->GetIp()}:21/{$dir}";

        $content = file_get_contents($link);

        $this->Display('default/ftp/edit', array(
            'cur_path' => $dir,
            'content' => $content
        ));
    }


    /**
     * Renders a view with a list of files and folders on a server.
     *
     * @param int $gsid the id of the game server to browse files for.
     */
    public function Dir($gsid)
    {
        $this->_ValidateServerPermissions($gsid);
        $dir = '';
        if (isset($_GET['path'])) {
            $dir = $_GET['path'];
            if (substr($dir, strlen($dir) - 1) != '/') {
                $dir .= '/';
            }
        }
        $gameserver = GameServer::GetModelById($gsid);


        $link = "ftp://{$gameserver->GetFtpUser()}:{$gameserver->GetFtpPass()}@{$gameserver->GetIp()}:21/{$dir}";

        $items = array();

        if ($handle = opendir($link)) {
            while (false !== ($entry = readdir($handle))) {
                $item = new FtpItem();
                $type = 'unknown';
                try {
                    if (is_dir($link . $entry)) {
                        $type = 'dir';
                    } else if (in_array(pathinfo($link . $entry, PATHINFO_EXTENSION), array('txt', 'cfg'))) {
                        $type = 'file-editable';
                    } else {
                        $type = 'file-noneditable';
                    }
                } catch (Exception $e) {
                    $type = 'file-noneditable';
                }
                $item->filename = $entry;
                $item->fullpath = $dir . $entry;
                $item->type = $type;
                try {
                    $item->filetime = date("F d Y H:i:s", filemtime($link . $entry));
                } catch (Exception $e) {

                }
                array_push($items, $item);
            }


            $this->Display('default/ftp/dir_list', array(
                'items' => $items,
                'cur_path' => $dir
            ));
            closedir($handle);
        } else {

        }
    }

}

/**
 * Object to store ftp item data.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class FtpItem
{
    public $filename;
    public $fullpath;
    public $type;
    public $filetime;
}