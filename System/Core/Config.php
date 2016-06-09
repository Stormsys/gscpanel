<?php    
/**
 * Class for decoupling configuration from other classes.
 * has added saftey features such a single read mode meaning that configuration paramaters cannot be intercepted.
 * 
 * @package    GameServerControlPanel  
 * @subpackage System.Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Config
{      
    private static $_config_array = array();
    private static $_single_get_mode = false;

    public static function Set($namespace, $value)
    {
        $nodes = explode('.', $namespace);
        $config = &self::$_config_array;

        $i = 0;
        $last_node = count($nodes) - 1;

        foreach ($nodes as $node) 
        {
            if(!isset($config[$node])) //only ever set values that are not already set.
            {
                if($i != $last_node)
                    $config[$node] = array();
                else
                    $config[$node] = $value;
            }

            $config = &$config[$node];
            $i++;
        }
    }
    public static function Get($namespace)
    {
        $nodes = explode('.', $namespace);
        $config = &self::$_config_array;

        $i = 0;
        $last_node = count($nodes) - 1;

        foreach ($nodes as $node) 
        {
            if(isset($config[$node]))
            {
                if($i == $last_node)
                {
                    $value = $config[$node];

                    if(self::$_single_get_mode)
                    {
                        if(!is_array($config[$node]))
                            $config[$node] = "";
                        else
                            self::_EmptyNodes($config[$node]);
                    }

                    return !empty($value) ? $value : null;
                }
            }
            else
                return null;

            $config = &$config[$node];
            $i++;
        }
    }
    private static function _EmptyNodes(&$start_node)
    {
        if(is_array($start_node))
        {
            foreach ($start_node as &$subnode) {
                self::_EmptyNodes($subnode);
            }
        }
        else
        {
            $start_node = "";
        }
    }
    public static function EnableSingleReadMode()
    {
        self::$_single_get_mode = true;
    }







    /*--------------------------------------------
     * MySql Confiruation Section
     *--------------------------------------------*/                       
    private static $mysql = array( 
        'hostname'   => 'localhost',
        'username'   => 'gscp_root',
        'password'   => '123l45',
        'database'   => 'gscp_gcp',
        'tbl_prefix' => ''
    );
}   