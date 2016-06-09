<?php
/**
 * Configuration file for the system.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Config
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
Config::Set('Autoloader.BasePath', 		'./System/');
Config::Set('Autoloader.ReMap.App', 	'./App/');


Config::Set('Router.DefaultMethod',	 	'Invoke');
Config::Set('Router.DefaultController', 'DefaultController');
Config::Set('Router.ControllerSuffix', 	'Controller');

Config::Set('Router.Rules', array(
	'/^\/?api\/\/?(.*)?$/i' => '/api-$1',
	'/^\/?internal\/\/?(.*)?$/i' => '/internal-$1'
));

Config::Set('View.BasePath', './App/View/');

Config::Set('Database.MySql', array(
    'hostname'       =>  'localhost',
    'database'       =>  'gscpanel_database',
    'username'       =>  'gscpanel_root',
    'password'       =>  'CHANGE_ME',
    'tbl_prefix'     =>  ''
));
