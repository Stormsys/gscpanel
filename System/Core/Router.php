<?php
using('Common', 'Core');
using('Controller', 'Core');

/**
 * Update Description
 *
 * @package    GameServerControlPanel
 * @subpackage System.Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Router
{
    private static $_config = null;

    /**
     * Set the rules which the dispatcher should parse when re-writing the URL.
     *
     * @example array('/^\/?a\/?(.*)?$/i' => '/Default/Invoke/$1');   <br/>
     *          will redirect /a/* to /Default/Invoke/*
     * @param   array $rules the set of rules to use when re-writing the URL.
     * @return  void
     */
    public static function SetConfig($config)
    {
        self::$_config = $config;
    }

    public static function Dispatch($url)
    {
        $args = array();
        $method_name = !empty(self::$_config['DefaultMethod']) ? self::$_config['DefaultMethod'] : 'default';
        $remap_method = !empty(self::$_config['ReMapMethod']) ? self::$_config['ReMapMethod'] : '__ReMap';
        $controller_name = !empty(self::$_config['DefaultController']) ? self::$_config['DefaultController'] : 'default';


        $dispatchUrl = Common::StripDuplicates($url, '/');

        if (self::$_config['Rules'] !== null)
            $dispatchUrl = Common::StripDuplicates(self::_MapUrl($dispatchUrl), '/');

        if (Common::StringPrefixIs($dispatchUrl, '/'))
            $dispatchUrl = substr($dispatchUrl, 1);

        if (Common::StringSuffixIs($dispatchUrl, '/'))
            $dispatchUrl = substr($dispatchUrl, 0, -1);


        $dispatchUrlSplit = explode('/', $dispatchUrl);

        switch (count($dispatchUrlSplit)) {
            default:
                $args = array_slice($dispatchUrlSplit, 2);
            case 2:
                if ($dispatchUrlSplit[1] != '') $method_name = Common::CamalizeUrl(Common::CleanToAlphanumeric($dispatchUrlSplit[1]));
            case 1:
                if ($dispatchUrlSplit[0] != '') {
                    $package_parts = explode('-', strtolower($dispatchUrlSplit[0]));
                    $controller_name = array_pop($package_parts) . (isset(self::$_config['ControllerSuffix']) ? self::$_config['ControllerSuffix'] : '');
                    $packages = implode('.', $package_parts);
                }
                break;
        }
        $namespace = (!empty($packages)) ? '.' . $packages : '';

        $controller_name = ucfirst($controller_name);

        using($controller_name, 'App.Controller' . $namespace);


        $Controller = new $controller_name;

        //TODO: Update Exception Types
        if (!is_subclass_of($Controller, 'Controller')) {
            throw new Exception($controller_name . ' is not a valid controller.');
        } else if (method_exists($Controller, $remap_method) && is_callable(array($Controller, $remap_method)) && in_array($remap_method, get_class_methods($Controller))) {
            call_user_func(array($Controller, $remap_method), $method_name, $args);
        } else if (method_exists($Controller, $method_name) && is_callable(array($Controller, $method_name)) && in_array($method_name, get_class_methods($Controller))) {
            call_user_func_array(array($Controller, $method_name), $args);
        } else {
            throw new Exception('Controller(' . $controller_name . ') Found, but invalid method(' . $method_name . ') called.');
        }
    }

    private static function _MapUrl($url)
    {
        return preg_replace(array_keys(self::$_config['Rules']), array_values(self::$_config['Rules']), $url);
    }
}