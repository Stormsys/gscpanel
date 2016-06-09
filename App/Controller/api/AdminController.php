<?php
using('AjaxController', 'App.Lib');
using('GameServer', 'App.DAO');
using('GameServerTemplate', 'App.DAO');
using('DedicatedServer', 'App.DAO');
using('Authentication', 'App.Lib');

/**
 * Controller designed to handle ajax functionality within the admin area.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller.Api
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class AdminController extends AjaxController
{
	private $auth;

	/**
	 * validates that there is a user online and that they are an admin else denies access to the rest of the file.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->auth = new Authentication();
		if(!$this->auth->isLoggedIn() || !($this->auth->GetUser()->Permissions()->Has('admin')))
		{
			die();
		}
	}

	/**
	 * obtains the dedicated servers default ip address.
	 *
	 * @param int $id the id of the dedicated server.
	 */
	public function GetDedicatedserverIp($id)
	{
		$this->out(DedicatedServer::GetIp($id));
	}


	/**
	 * obtains the default data for the template specified.
	 *
	 * @param int $id the id of the template.
	 */
	public function GetTemplateData($id)
	{
		$this->out(GameServerTemplate::GetById($id)->fetch());
	}
}