<?php
/**
 * Redirects requests to the dashboard controller.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Controller
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class DefaultController extends Controller
{
    public function Invoke()
    {
        header('location: /dashboard');
    }
}