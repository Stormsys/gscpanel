<?php
using('AjaxController', 'App.Lib');
using('GameServer', 'App.DAO');
using('GameServerStatus', 'App.Model');
using('Authentication', 'App.Lib');
using('Queue', 'App.DAO');
using('QueueEvent', 'App.Model');

/**
 * Api provided for clients to control servers via ajax.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller.Api
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class GameserversController extends AjaxController
{
    private $auth;

    /**
     * loading prerequisites before methods are called.
     */
    public function __construct()
    {
        parent::__construct();
        $this->auth = new Authentication();
    }

    /**
     * internal method to ensure that a user is logged in before processing.
     */
    private function _ForceAuthenticate()
    {
        if (!$this->auth->isLoggedIn()) {
            $this->Error('You are not logged in!');
            die();
        }
    }

    /**
     * API call to obtain a list of servers and their status' for the current user.
     */
    public function StatusAll()
    {
        try {
            $this->_ForceAuthenticate();

            $result = GameServer::GetServersStatusForUser($this->auth->GetUser()->GetId())->fetchAll();

            foreach ($result as &$value) {
                $value['literal_status'] = GameServerStatus::$STYLE_TEXT[$value['online_status']];
            }

            $this->out($result);
        } catch (Exception $e) {
            $this->Error($e->getMessage());
        }
    }

    /**
     * internal method for validating permissions before allowing actions to be performed.
     *
     * @param int $gsid the id of the game server.
     */
    private function _ValidateServerPermissions($gsid)
    {
        try {
            $this->_ForceAuthenticate();
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
     * internal method for updating the game server status to pending while waiting for queue to be processed.
     *
     * @param int $gsid the id of the game server.
     */
    private function _SetStatusPending($gsid)
    {
        try {
            GameServer::SetServerStatus($gsid, GameServerStatus::PENDING);
        } catch (Exception $e) {
            $this->Error($e->getMessage());
        }
    }


    /**
     * Queue a server to be started over AJAX.
     *
     * @param int $gsid the id of the game server.
     */
    public function Start($gsid)
    {
        try {
            $this->_ValidateServerPermissions($gsid);

            $model = GameServer::GetModelById($gsid);
            $currentStatus = $model->GetStatus();

            if ($model->GetId() != $gsid) {
                throw new Exception('Invalid server specified.');
            }

            if ($currentStatus != GameServerStatus::ONLINE && $currentStatus != GameServerStatus::PENDING && $currentStatus != GameServerStatus::INSTALLING) {
                $this->_SetStatusPending($gsid);
                Queue::AddAction($gsid, QueueEvent::START_SERVER);

                $this->Out(null);
            } else {
                $this->Error('Cannot start the server as it is already online or there is an action pending!');
            }
        } catch (Exception $e) {
            $this->Error($e->getMessage());
        }
    }


    /**
     * Queue a server to be reinstalled over AJAX.
     *
     * @param int $gsid the id of the game server.
     */
    public function Reinstall($gsid)
    {
        try {
            $this->_ValidateServerPermissions($gsid);


            $model = GameServer::GetModelById($gsid);
            $currentStatus = $model->GetStatus();

            if ($model->GetId() != $gsid) {
                throw new Exception('Invalid server specified.');//test 345 fix
            }


            if ($currentStatus != GameServerStatus::PENDING && $currentStatus != GameServerStatus::INSTALLING) {
                $this->_SetStatusPending($gsid);
                Queue::AddAction($gsid, QueueEvent::REINSTALL_SERVER);
                $this->Out(null);
            } else {
                $this->Error('Cannot reinstall the server as it is already being installed or there is an action pending!');
            }
        } catch (Exception $e) {
            $this->Error($e->getMessage());
        }
    }


    /**
     * Queue a server to be stopped over AJAX.
     *
     * @param int $gsid the id of the game server.
     */
    public function Stop($gsid)
    {
        try {

            $this->_ValidateServerPermissions($gsid);

            $model = GameServer::GetModelById($gsid);
            $currentStatus = $model->GetStatus();

            if ($model->GetId() != $gsid) {
                throw new Exception('Invalid server specified.');
            }

            if ($currentStatus != GameServerStatus::OFFLINE && $currentStatus != GameServerStatus::PENDING && $currentStatus != GameServerStatus::INSTALLING) {
                $this->_SetStatusPending($gsid);
                Queue::AddAction($gsid, QueueEvent::STOP_SERVER);
                $this->Out(null);
            } else {
                $this->Error('Cannot start the server as it is already offline or there is an action pending!');
            }
        } catch (Exception $e) {
            $this->Error($e->getMessage());
        }
    }

    /**
     * Queue a server to be restarted over AJAX.
     *
     * @param int $gsid the id of the game server.
     */
    public function Restart($gsid)
    {
        try {
            $this->_ValidateServerPermissions($gsid);

            $model = GameServer::GetModelById($gsid);
            $currentStatus = $model->GetStatus();

            if ($model->GetId() != $gsid) {
                throw new Exception('Invalid server specified.');
            }

            if ($currentStatus != GameServerStatus::PENDING && $currentStatus != GameServerStatus::INSTALLING) {
                $this->_SetStatusPending($gsid);
                Queue::AddAction($gsid, QueueEvent::RESTART_SERVER);
                $this->Out(null);
            } else {
                $this->Error('Cannot restart the server as there is an action pending!');
            }
        } catch (Exception $e) {
            $this->Error($e->getMessage());
        }
    }

}