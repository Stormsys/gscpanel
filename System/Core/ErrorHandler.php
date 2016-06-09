<?php
/**
 * TODO: Update Description
 * 
 * @package    GameServerControlPanel  
 * @subpackage System.Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class ErrorHandler
{
    private static $_instance = null;
    
    public function __construct()
    {
        set_error_handler(array($this, 'OnError'), E_ALL & ~E_NOTICE);
    }        
    public function OnError($errno, $errstr, $errfile, $errline ) {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    }     
    
             
    public static function Load()
    {
        self::GetInstance();
    }
    public static function GetInstance()
    {
        if(self::$_instance === null) 
            self::$_instance = new ErrorHandler();  
                    
        return self::$_instance;
    }
}