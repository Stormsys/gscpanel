<?php
using('SSH', 'Core');
using('GameServerModel', 'App.Model');
using('GameServerStatus', 'App.Model');
using('DedicatedServer', 'App.DAO');
using('GameServerTemplate', 'App.DAO');
using('GameServer', 'App.DAO');
using('SSHManager', 'App.Lib');

/**
 * Provides a managment api for game servers.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Lib
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class GameserverManager
{
    private $_ssh = null;
    private $_base_url = 'http://test.gscpanel.com';

    public function __construct(SSHManager $ssh)
    {
        $this->_ssh = $ssh;
    }

    /**
     * Generates a random password salt to be used for the mcrypt library
     */
    private function _GeneratePassSalt()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars), 0, 2);
    }

    private function _GenerateCommandLine(GameServerModel $gameserver)
    {
        $cmd_line = $gameserver->GetCommandLine();
        $cmd_line = str_replace('{port}', $gameserver->GetPort(), $cmd_line);
        $cmd_line = str_replace('{ip}', $gameserver->GetIP(), $cmd_line);
        $cmd_line = str_replace('{slots}', $gameserver->GetSlots(), $cmd_line);

        return $cmd_line;
    }

    public function Delete(GameServerModel $gameserver)
    {
        echo "Deleting server #{$gameserver->GetId()}.\n";
        $this->StopServer($gameserver);
        $this->_ssh->Exec($gameserver->GetDedicatedServerId(), "rm -rf {$gameserver->GetServerDir()}");
        $this->_ssh->Exec($gameserver->GetDedicatedServerId(), "userdel -f {$gameserver->GetFtpUser()}");
        GameServer::Delete($gameserver->GetId());
    }

    public function UpdateFTPPass(GameServerModel $gameserver)
    {
        echo "Updating FTP password for server #{$gameserver->GetId()}.\n";
        $password = crypt($gameserver->GetFtpPass(), $this->_GeneratePassSalt());

        $this->_ssh->Exec($gameserver->GetDedicatedServerId(), "usermod -p {$password} {$gameserver->GetFtpUser()}");
    }

    public function Install(GameServerModel $gameserver)
    {
        echo "Setting up user for server #{$gameserver->GetId()}.\n";
        $password = crypt($gameserver->GetFtpPass(), $this->_GeneratePassSalt());
        $this->_ssh->Exec($gameserver->GetDedicatedServerId(), "useradd -p {$password} -d {$gameserver->GetServerDir()} {$gameserver->GetFtpUser()}");

        $this->Reinstall($gameserver);
    }

    public function Reinstall(GameServerModel $gameserver)
    {
        echo "Reinstalling server #{$gameserver->GetId()}.\n";
        GameServer::SetServerStatus($gameserver->GetId(), GameServerStatus::INSTALLING);

        $this->StopServer($gameserver, false);

        $dedicated_server = DedicatedServer::GetModelById($gameserver->GetDedicatedServerId());
        $gameserver_template = GameServerTemplate::GetModelById($gameserver->GetTemplateId());

        $zip_dir = $dedicated_server->GetInstallDir();
        $zip_file = $gameserver_template->GetZipFilename();

        $gs_dir = $gameserver->GetServerDir();


        $cmd = "screen -A -m -d -S gscp-install-{$gameserver->GetId()} bash -c 'rm -rf {$gs_dir}/*; unzip {$zip_dir}/{$zip_file} -d {$gs_dir}; chown -R {$gameserver->GetFtpUser()}:{$gameserver->GetFtpUser()} {$gs_dir}/*; curl {$this->_base_url}/api/queue/gameserver-stopped/{$gameserver->GetId()}'";

        $this->_ssh->Exec($gameserver->GetDedicatedServerId(), $cmd);
    }

    public function UpdateStatus(GameServerModel $gameserver)
    {
        $cmd_get_pids = 'screen -ls | awk \'/\\.gscp-' . $gameserver->GetId() . '\\t/ {print strtonum($1)}\'';
        $screen_ids = explode("\n", $this->_ssh->Exec($gameserver->GetDedicatedServerId(), $cmd_get_pids));
        array_pop($screen_ids); //get rid of the blank

        $instances = count($screen_ids);

        if ($instances > 0) {
            $currentStatus = $gameserver->GetStatus();
            if ($currentStatus != GameServerStatus::ONLINE && $currentStatus != GameServerStatus::PENDING && $currentStatus != GameServerStatus::INSTALLING) {
                echo "GameServer #{$gameserver->GetId()} was updated to be online.";
                GameServer::SetServerStatus($gameserver->GetId(), GameServerStatus::ONLINE);
            }
        } else {
            $currentStatus = $gameserver->GetStatus();
            if ($currentStatus != GameServerStatus::OFFLINE && $currentStatus != GameServerStatus::PENDING && $currentStatus != GameServerStatus::INSTALLING) {
                echo "GameServer #{$gameserver->GetId()} was updated to be offline.";
                GameServer::SetServerStatus($gameserver->GetId(), GameServerStatus::OFFLINE);
            }
        }
    }

    public function StartServer(GameServerModel $gameserver)
    {
        $cmd = "cd {$gameserver->GetServerDir()}; screen -A -m -d -S gscp-{$gameserver->GetId()} {$this->_GenerateCommandLine($gameserver)}";

        echo "Starting GameServer #{$gameserver->GetId()}.\n";
        $this->_ssh->Exec($gameserver->GetDedicatedServerId(), $cmd);
        $this->_ssh->Exec($gameserver->GetDedicatedServerId(), 'curl http://test.gscpanel.com/api/queue/gameserver-started/' . $gameserver->GetId());
    }

    public function RestartServer(GameServerModel $gameserver)
    {
        echo 'Restarting';
        $this->StopServer($gameserver, false);
        $this->StartServer($gameserver);
    }

    public function StopServer(GameServerModel $gameserver, $update = true)
    {
        $cmd_get_pids = 'screen -ls | awk \'/\\.gscp-' . $gameserver->GetId() . '\\t/ {print strtonum($1)}\'';
        $screen_ids = explode("\n", $this->_ssh->Exec($gameserver->GetDedicatedServerId(), $cmd_get_pids));
        array_pop($screen_ids); //get rid of the blank

        $instances = count($screen_ids);

        echo "Stopping GameServer #{$gameserver->GetId()}, Found {$instances} Instances.\n";
        foreach ($screen_ids as $screen_id) {
            echo "Terminating Screen With PID $screen_id.\n";
            $this->_ssh->Exec($gameserver->GetDedicatedServerId(), "pkill -TERM -P $screen_id");
            $this->_ssh->Exec($gameserver->GetDedicatedServerId(), "screen -D {$screen_id}.gscp-{$gameserver->GetId()} -X quit");
        }

        if ($update)
            $this->_ssh->Exec($gameserver->GetDedicatedServerId(), "curl {$this->_base_url}/api/queue/gameserver-stopped/{$gameserver->GetId()}");
    }
}