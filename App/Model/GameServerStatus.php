<?php
/**
 * Models a status types on the system.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Model
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class GameServerStatus
{
    const OFFLINE = 1;
    const ONLINE = 2;
	const RESTARTING = 3;
	const PENDING = 4;
	const INSTALLING = 5;

	static $STYLE_TEXT = array(
		1 => 'offline',
		2 => 'online',
		3 => 'restarting',
		4 => 'pending',
		5 => 'installing'
	);
}