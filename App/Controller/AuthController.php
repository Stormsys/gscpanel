<?php
using('PageController', 'App.Lib');
using('Authentication', 'App.Lib');

/**
 * Provides login functionality.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class AuthController extends PageController
{
	private $auth;
	public function __construct()
	{
		parent::__construct();
		$this->auth = new Authentication();

		if($this->auth->isLoggedIn())
		{
			header('location: /dashboard');

			die();
		}
	}
	public function Auth()
	{

	}


	/**
	 * Renders the login page if user is not logged in.
	 */
	public function Login()
	{
		header('PAGE_STATE: login');
		$this->Display('auth/login');
	}
}