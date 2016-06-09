<?php
using('Controller', 'Core');

/**
 * Abstracts template and enables page transitioning functonality via ajax.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Lib
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class PageController extends Controller
{
    public function __construct()
    {
        $this->SetTemplate('default');
		$auth = new Authentication();

		if($auth->isLoggedIn() && $auth->GetUser()->Permissions()->Has('admin'))
			header('IS_ADMIN: TRUE');
		if($auth->isLoggedIn())
			header('IS_LOGGED_IN: TRUE');

		header('REQUEST_URL: ' . urldecode($_SERVER['QUERY_STRING']));
       // GSCP_Core()->Session();
    }
    protected function IsAjaxRequest()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
    public function __ReMap($method_name, $args)
    {
        $class_reflection  = new ReflectionClass(get_class($this));
        $method_data = $class_reflection->getMethod($method_name);

        if($method_data->isPublic())
        {
            $ajax_request  = $this->IsAjaxRequest();

            if(!$ajax_request) $this->Display('header');
			call_user_func_array(array($this, $method_name), $args);
            if(!$ajax_request) $this->Display('footer');
        }
        else
        {
            throw new Exception('Method not found...');
        }
    }
}