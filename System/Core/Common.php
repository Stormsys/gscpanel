<?php
/**
 * Contains common functionallity which does not nessosarrily belong to any other class.
 * 
 * @package    GameServerControlPanel  
 * @subpackage System.Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/             
class Common 
{               
    //TODO: Document File  
    public static function StringSuffixIs($str, $suffix)
    {
        $length = strlen($suffix);
        return ($length < strlen($str) && substr($str, -$length, $length) == $suffix);
    }         
    public static function StringPrefixIs($str, $prefix)
    {
        $length = strlen($prefix);
        return ($length < strlen($str) && substr($str, 0, $length) == $prefix);
    }    
    public static function StripDuplicates($subject, $search)
    {
        $duplicate = $search . $search;
        while(strpos($subject, $duplicate))
        {
             $subject = str_replace($duplicate, $search, $subject);
        }
        return $subject;
    }
    public static function CleanToAlphanumeric($input)
    {
		//note alpha numeric + -
        return preg_replace('/[^a-zA-Z0-9-]/', '', $input);
    }
    public static function CamalizeUrl($url)
    {
        return ucfirst(preg_replace_callback("/-(.)/",
            function ($match)
            {
                return strtoupper($match[1]);
            },
            $url)
        ); 
    }
}