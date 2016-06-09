<?php
require_once('Benchmark.php');

using('ErrorHandler', 'Core');
using('Database', 'Database');
using('Session', 'Core');
using('Template', 'Core', true);

/**
 * TODO: Update Description
 *
 * @package    GameServerControlPanel
 * @subpackage System.Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Core
{
    private $_mysql_instance = null;
    private $_session_instance = null;
    private static $_instance = null;
    private static $_version = null;

    private function __construct()
    {
        ob_start();

        Benchmark::Mark('start');

        $this->CleanPost();

        ErrorHandler::Load();
        self::GetVersion();


        define('GSCP_LOADED', true);
    }

    private function CleanPost()
    {
        foreach ($_POST as &$val) {
            $val = $this->XSSClean($val);
        }
    }

    private function XSSClean($data)
    {
        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }

    public function Finalize()
    {

    }

    public function __destruct()
    {
        $output = ob_get_clean();

        Template::Set('class_load', Autoloader::GetInstance()->GetFilesLoadedCount());
        Template::Set('version', self::GetVersion());
        Template::Set('load_time', Benchmark::Get('start'));

        $output = Template::Parse($output);
        echo $output;
    }


    /**
     * Database
     *
     * Returns the framework persistant mysql instance.
     *
     * @return MySql
     */
    public function Database()
    {
        if ($this->_mysql_instance === null) {
            $sqlConfig = Config::Get('Database.MySql');
            $this->_mysql_instance = new Database($sqlConfig['hostname'], $sqlConfig['username'], $sqlConfig['password'], $sqlConfig['database'], $sqlConfig['tbl_prefix']);
        }

        return $this->_mysql_instance;
    }

    public function Session()
    {
        if ($this->_session_instance === null) {
            $this->_session_instance = new Session();
        }

        return $this->_session_instance;
    }


    public static function Load()
    {
        self::GetInstance();
    }

    public static function GetVersion()
    {
        if (self::$_version == null)
            self::$_version = file_get_contents('./VERSION');

        return self::$_version;
    }

    public static function GetInstance()
    {
        if (self::$_instance === null)
            self::$_instance = new Core();

        return self::$_instance;
    }
}
