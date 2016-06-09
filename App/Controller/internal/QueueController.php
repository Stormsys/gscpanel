<?php
using('CronController', 'App.Lib');
using('GameserverManager', 'App.Lib');
using('SSHManager', 'App.Lib');
using('GameServer', 'App.DAO');
using('Queue', 'App.DAO');
using('QueueEvent', 'App.Model');

/**
 * Provides command line processing of queue, designed to be invoked via Cron.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller.Internal
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class QueueController extends CronController
{
	private $_gs_man = null;
    public function __construct()
    {
        parent::__construct();
		$this->_gs_man = new GameserverManager(new SSHManager());
    }

	/**
	 * Cleanup items in the queue which has been processed.
	 */
	public function CleanUp()
	{
		echo 'Removing all processed items from queue table...';
		Queue::DeleteAllInProgress();
		echo "Done...\n";
	}


	/**
	 * Provides console with method to update status' of game servers on the system.
	 */
	public function Status()
	{
		$server_result = GameServer::GetAllIds();
		while($server = $server_result->fetch())
		{
			$this->_gs_man->UpdateStatus(GameServer::GetModelById($server['gs_id']));
		}
	}

    public function Invoke()
	{
   		//$this->_gs_man->StartServer(GameServer::GetModelById(1));
		$this->_NextItem();
    }


	/**
	 * Internal method for obtaining the next item in the queue.
	 */
	private function _NextItem()
	{
		$data = Queue::GetNextItem()->fetch();
		if(!empty($data))
			$this->_ProcesItem($data);
	}


	/**
	 * Internal method for parsing a single queue item and delegates the appropirate action.
	 *
	 * @param array $item a queue item from the database
	 */
	private function _ProcesItem($item)
	{

		Queue::InProgress($item['action_id']);
		$data = unserialize($item['data']);

		$model = GameServer::GetModelById($item['gs_id']);
		if($model->GetId() == $item['gs_id'])
		{
			switch($item['type'])
			{
				case QueueEvent::START_SERVER:
					echo "Start Event \n";
					$this->_gs_man->StartServer(GameServer::GetModelById($item['gs_id']));
					break;
				case QueueEvent::STOP_SERVER:
					echo "Stop Event \n";
					$this->_gs_man->StopServer(GameServer::GetModelById($item['gs_id']));
					break;
				case QueueEvent::RESTART_SERVER:
					echo "Restart Event \n";
					$this->_gs_man->RestartServer(GameServer::GetModelById($item['gs_id']));
					break;
				case QueueEvent::REINSTALL_SERVER:
					echo "Reinstall Event \n";
					$this->_gs_man->Reinstall(GameServer::GetModelById($item['gs_id']));
					break;
				case QueueEvent::INSTALL_SERVER:
					echo "Install Event \n";
					$this->_gs_man->Install(GameServer::GetModelById($item['gs_id']));
				case QueueEvent::UPDATE_FTP_PASSWORD:
					echo "Update FTP Event \n";
					$this->_gs_man->UpdateFTPPass(GameServer::GetModelById($item['gs_id']));
					break;
				case QueueEvent::DELETE_SERVER:
					echo "Delete Event \n";
					$this->_gs_man->Delete(GameServer::GetModelById($item['gs_id']));
					break;
				default:
					echo "Unknown Event \n";
					break;
			}

			$this->_NextItem();
		}
	}
}