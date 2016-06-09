<?php
using('MissingClassException', 'Error');

/**
 * class to handle autoloading in the project, this allows weak coupling within classes.
 *
 * @package    GameServerControlPanel  
 * @subpackage Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Autoloader
{          
    private static $_instance = null;
    private static $_config_array = array();    
    private static $_class_list = array(); 
    private static $_load_count = 0;
    
    private function __construct()
    {                             
        //register myself as King Autoloader.
        spl_autoload_register(array($this, '_Autoload')); 
    } 
    private function _Autoload($class_name)
    {
        $base_path = isset(self::$_config_array['BasePath']) ? self::$_config_array['BasePath'] : './System';
        
        $context = isset(self::$_class_list[$class_name]) ? self::$_class_list[$class_name] : '';
        
        if(empty($context))
        {
            $file_path = $base_path . '/' . $class_name . '.php';
        }
        else
        {
            $i = 0;
            foreach(explode('.', $context) as $context_part)
            {
                if($i == 0)
                    $computed_path = isset(self::$_config_array['ReMap'][$context_part]) ? self::$_config_array['ReMap'][$context_part] : $base_path . '/' . $context_part;
                else
                    $computed_path .= '/' . $context_part;
                    
                $i++;
            }
            $file_path = $computed_path . '/' . $class_name . '.php';
        }

        $loaded = $this->_LoadFile($file_path);

        if(!$loaded || (!class_exists($class_name) && !interface_exists($class_name)))
            throw new MissingClassException($class_name, 'Unable to process the request, file ' . $file_path . ' not found', $loaded);

        self::$_load_count++;
    }
    
    /**
     * _LoadFile
     * 
     * Attempts to load the file specified.  
     * 
     * @param string $fileName is the filename and path to the file you are attempting to load.
     * @return boolean
     */
    private function _LoadFile($fileName)
    {
        if(file_exists($fileName)){
            require($fileName);              
            return true;
        }
        return false;
    }  
    
    public function Using($class, $context, $load_now = false)
    {
        self::$_class_list[$class] = $context;

        if($load_now)
            $this->_Autoload($class);
    }
    public function GetFilesLoadedCount()
    {
        return self::$_load_count;
    }
    public static function SetConfig($config_array){
        self::$_config_array = $config_array;
    }
    public static function GetInstance()
    {
        if(self::$_instance === null)
            self::$_instance = new Autoloader();
                            
        return self::$_instance;
    }
}

/*------------------------------------------------------------
 * Including this file implies that you are going to load it.
 *------------------------------------------------------------*/
function using($class, $context, $load_now = false){
    Autoloader::GetInstance()->Using($class, $context, $load_now);
}