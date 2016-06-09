<?php
using('SSH', 'Core');
using('DedicatedServer', 'App.DAO');

/**
 * Provides a managment utility for mapping dedicated servers to SSH instances.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Lib
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class SSHManager
{
	private $_connections = array();
	public function __construct()
	{
	}
	private function _Connect($dsid)
	{
		if(!isset($this->_connections[$dsid]))
		{
			$dedicated_server = DedicatedServer::GetModelById($dsid);
			$this->_connections[$dsid] = new SSH($dedicated_server->GetSSHIp(), $dedicated_server->GetSSHPort(), $dedicated_server->GetSSHUsername(), $dedicated_server->GetSSHPassword());

		}
	}
	public function Exec($dsid, $command)
	{
		$this->_Connect($dsid);
		return $this->_connections[$dsid]->Run($command);
	}

}