<?php
/** 
 * TODO: Update Description
 *                  
 * 
 * @package    GameServerControlPanel  
 * @subpackage System.Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/     
class FlashMemory
{                 
    private static $_instance = null;
    
    private $lastPageData = null;
    private $thisPageData = array();
    
    public function __construct()
    {    
        $this->lastPageData = array();
        if(isset($_SESSION['FlashMemory']))  
            $this->lastPageData = $_SESSION['FlashMemory'];       
    }
    private function _Get($key)
    {
        return $this->lastPageData[$key];   
    }   
    private function _Set($key, $value)
    {
        $this->thisPageData[$key] = $value;   
    }
    public function __destruct()
    {                
        $_SESSION['FlashMemory'] = $this->thisPageData;
    }
    
    public static function Load()
    {
        if(self::$_instance === null)
        {
           self::$_instance = new FlashMemory();
        }
    } 
    public static function Get($key)
    {    
        return self::$_instance->_Get($key);
    }
    public static function Set($key, $value)
    {      
        self::$_instance->_Set($key, $value);
    }
}