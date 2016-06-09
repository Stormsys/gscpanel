<?php
using('PageController', 'App.Lib');
using('GameServer', 'App.DAO');
using('QueueController', 'App.Controller');
using('SSH', 'Core');
using('GameServerStatus', 'App.Model');
using('Authentication', 'App.Lib');

/**
 * Provides dashboard functionality to the user.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class DashboardController extends PageController
{
	private $auth;
    public function __construct()
    {
        parent::__construct();
		$this->auth = new Authentication();
		if(!$this->auth->isLoggedIn())
		{
			header('location: /auth/login');

			die();
		}
    }


	/**
	 * Provides a list of servers for the account.
	 */
    public function Invoke()
    {
		header('PAGE_STATE: dashboard');
        $game_servers = GameServer::GetServersForUser($this->auth->GetUser()->GetId())->fetchAll();

        $this->Display('dashboard/server_list', array(
            'game_servers' => $game_servers
        ));
    }


	/**
	 * Provides the manage game server page
	 *
	 * @param int $gsid the id of the game server to manage.
	 */
	public function Manage($gsid)
	{

		header('PAGE_STATE: dashboard');
		$gs_model = GameServer::GetModelById($gsid);
		if(!($this->auth->GetUser()->Permissions()->Has('admin') || $gs_model->GetOwnerId() == $this->auth->GetUser()->GetId()))
		{
			echo 'Invalid Permissions';
			die();
		}
		$this->Display('dashboard/manage_home', array(
			'server' =>  $gs_model
		));
	}

	/**
	 * Provides the manage account page.
	 */
	public function Account()
	{
		header('PAGE_STATE: account');
		$this->Display('dashboard/manage_account', array(
			'user' => $this->auth->GetUser()
		));
	}

}