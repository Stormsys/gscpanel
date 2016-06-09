<?php
using('Controller', 'Core');

/**
 * Provides ajax specific methods to be inhereted by controllers.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Lib
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class AjaxController extends Controller
{
    public function __construct()
    {
        // if(!$this->IsAjaxRequest())
        //      throw new Exception('todo: 404 exception');

        header('Content-Type: application/json');
    }

    protected function IsAjaxRequest()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    protected function Out($data)
    {
        echo json_encode(array('success' => true, 'data' => $data));
    }

    protected function Error($message)
    {
        echo json_encode(array(
            'success' => false,
            'error_message' => $message
        ));
    }
}