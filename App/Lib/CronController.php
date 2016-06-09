<?php
using('Controller', 'Core');

/**
 * Provides a terminal layer for controller to inherit from, only allows usage from console.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Lib
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class CronController extends Controller
{
    public function __construct()
    {
        if (!defined('STDIN'))
            die();
    }
}