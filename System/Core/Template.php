<?php

/**
 * Provides parsing of {tags}, used by core on destruct for late rendering.
 *
 * @package    GameServerControlPanel
 * @subpackage System.Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Template
{
    private static $_arrTemplateVars = array();

    public static function Set($key, $value)
    {
        self::$_arrTemplateVars[$key] = $value;
    }

    public static function Get($key)
    {
        if (!isset(self::$_arrTemplateVars[$key])) return null;
        return self::$_arrTemplateVars[$key];
    }

    public static function Parse($data)
    {
        $matches = array();
        preg_match_all('/\{(\w*)\}/', $data, $matches);
        foreach ($matches[1] as $key) {
            if (($replaceWith = self::Get($key)) !== null)
                $data = str_replace('{' . $key . '}', $replaceWith, $data);
        }

        return $data;
    }
} 