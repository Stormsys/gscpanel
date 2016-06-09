<?php
chdir(dirname(__FILE__));

require_once('./System/Core/Autoloader.php');
 
using('Core',   'Core');
using('Router', 'Core');
using('Config', 'Core');
using('View',   'Core');

/**
 * Front-Facing controller
 *
 * @package    GameServerControlPanel
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Program
{ 
    public function __construct()
    {
		header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );

        require_once('./App/Config/Config.php');

        try{
            Config::EnableSingleReadMode();
            Autoloader::SetConfig(Config::Get('Autoloader'));

            Core::Load();
            View::SetBasePath(Config::Get('View.BasePath'));

			$arguments = getopt("q:");

			if(defined('STDIN') || isset($arguments['q']))
			{
				if(!defined('STDIN')) define('STDIN', true);
				$query_string = $arguments['q'];
			}else{
            	$query_string =  isset($_SERVER['QUERY_STRING']) ? urldecode($_SERVER['QUERY_STRING']) : '';
			}

            Router::SetConfig(Config::Get('Router'));
            Router::Dispatch($query_string);

            GSCP_Core()->Finalize();

			if(defined('STDIN') || isset($arguments['q']))
				echo "\n";
        }catch(Exception $e){
			//handle errors here...
            echo "<pre>";
            print_r($e);
            echo "</pre>";
			echo "\n\nError Message: " . $e->getMessage() . "\n\n";
        }

    }
}
function GSCP_Core() {
    return Core::GetInstance();
}

new Program();   