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
 
class Benchmark
{
    private static $_benchmarks = array();    
    public static function Mark($name)
    {
        self::$_benchmarks[$name] = microtime();        
    }
    public static function Get($from, $to = null)
    {
        $first = self::$_benchmarks[$from];
        if(!isset($to))           
            $second = microtime();
        else             
            $second = self::$_benchmarks[$to];
            
        return round($second - $first, 4) * 10000;        
    }
}   