<?php
/**
 * Models a queue events.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Model
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class QueueEvent
{
    const STOP_SERVER = 0;
    const START_SERVER = 1;
	const RESTART_SERVER = 2;
	const REINSTALL_SERVER = 3;
	const INSTALL_SERVER = 4;
	const UPDATE_FTP_PASSWORD = 5;
	const DELETE_SERVER = 6;

}