<?php
using('AjaxController', 'App.Lib');
using('GameServer', 'App.DAO');
using('GameServerStatus', 'App.Model');

/**
 * Queue Controller for managing response from servers for Queue Actions.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller.Api
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class QueueController extends AjaxController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Sets a game server status to online.
     *
     * @param int $gsid the id of the game server.
     */
    public function GameserverStarted($gsid)
    {
        GameServer::SetServerStatus($gsid, GameServerStatus::ONLINE);
    }


    /**
     * Sets a game server status to offline.
     *
     * @param int $gsid the id of the game server.
     */
    public function GameserverStopped($gsid)
    {
        GameServer::SetServerStatus($gsid, GameServerStatus::OFFLINE);
    }


    /**
     * Sets a game server status to restarting.
     *
     * @param int $gsid the id of the game server.
     */
    public function GameserverRestarting($gsid)
    {
        GameServer::SetServerStatus($gsid, GameServerStatus::RESTARTING);
    }
}