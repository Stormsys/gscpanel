<?php

/**
 * Models a game server on the system.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Model
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class GameServerModel
{
    private $id;
    private $dserver_id;
    private $template_id;
    private $owner_id;
    private $nickname;
    private $ip;
    private $port;
    private $parm_override;
    private $slot_count;
    private $ftp_username;
    private $ftp_password;
    private $status;
    private $server_dir;

    public function __construct($id, $dsid, $templateid, $ownerid, $nickname, $ip, $port, $param_or,
                                $slot_count, $ftp_user, $ftp_pass, $status, $server_dir)
    {
        $this->id = $id;
        $this->dserver_id = $dsid;
        $this->template_id = $templateid;
        $this->owner_id = $ownerid;
        $this->nickname = $nickname;
        $this->ip = $ip;
        $this->port = $port;
        $this->parm_override = $param_or;
        $this->slot_count = $slot_count;
        $this->ftp_username = $ftp_user;
        $this->ftp_password = $ftp_pass;
        $this->status = $status;
        $this->server_dir = $server_dir;
    }

    public function GetId()
    {
        return $this->id;
    }

    public function GetDedicatedServerId()
    {
        return $this->dserver_id;
    }

    public function GetStatus()
    {
        return $this->status;
    }

    public function GetCommandLine()
    {
        return $this->parm_override;
    }

    public function GetPort()
    {
        return $this->port;
    }

    public function GetIP()
    {
        return $this->ip;
    }

    public function GetOwnerId()
    {
        return $this->owner_id;
    }

    public function GetTemplateId()
    {
        return $this->template_id;
    }

    public function GetSlots()
    {
        return $this->slot_count;
    }

    public function GetServerDir()
    {
        return $this->server_dir;
    }

    public function GetNickname()
    {
        return $this->nickname;
    }

    public function GetFtpUser()
    {
        return $this->ftp_username;
    }

    public function GetFtpPass()
    {
        return $this->ftp_password;
    }
}
