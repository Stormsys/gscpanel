<?php
/**
 * Models a dedicated server on the system.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Model
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class DedicatedServerModel
{
	private $id = null;
	private $main_ip;
	private $ssh_ip;
	private $ssh_port;
	private $ssh_username;
	private $ssh_password;
	private $installs_dir;
	private $nickname;

	public function __construct($id, $main_ip, $ssh_ip, $ssh_port, $ssh_username, $ssh_password, $installs_dir, $nickname)
	{
		$this->id = $id;
		$this->main_ip = $main_ip;
		$this->ssh_ip = $ssh_ip;
		$this->ssh_port = $ssh_port;
		$this->ssh_username = $ssh_username;
		$this->ssh_password = $ssh_password;
		$this->installs_dir = $installs_dir;
		$this->nickname = $nickname;
	}
	public function GetID()
	{
		return $this->id;
	}
	public function GetMainIP()
	{
		return $this->main_ip;
	}
	public function GetSSHIp()
	{
		return $this->ssh_ip;
	}
	public function GetNickname()
	{
		return $this->nickname;
	}
	public function GetSSHUsername()
	{
		return $this->ssh_username;
	}
	public function GetSSHPort()
	{
		return $this->ssh_port;
	}
	public function GetSSHPassword()
	{
		return $this->ssh_password;
	}
	public function GetInstallDir()
	{
		return $this->installs_dir;
	}
}
