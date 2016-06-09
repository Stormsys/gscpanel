<?php
using('FileNotFoundException', 'Error');

/**
 * TODO: Update Description
 *
 * @package    GameServerControlPanel
 * @subpackage System.Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class View
{
    private $_variables = array();
    private $_filePath = '';
    private static $_base_path = '';

    public static function SetBasePath($base_path)
    {
        self::$_base_path = $base_path;
    }

    public function __construct($path, $vars = null)
    {
        $this->_filePath = self::$_base_path . $path . '.php';

        if (!file_exists($this->_filePath))
            throw new FileNotFoundException('Requested view, ' . $path . ' is missing.', $this->_filePath);

        if ($vars !== null)
            $this->assign($vars);
    }

    public function Assign($var, $value = null)
    {
        if (is_array($var) && $value === null) {
            foreach ($var as $key => $val) {
                $this->_variables[$key] = $val;
            }
        } else
            $this->_variables[$var] = $value;
    }

    public function Render()
    {
        ob_start();

        extract($this->_variables);
        include($this->_filePath);

        return ob_get_clean();
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $e) {
            echo 'Exception: ', $e->getMessage();
        }
    }

    public function Display()
    {
        echo $this;
    }
}