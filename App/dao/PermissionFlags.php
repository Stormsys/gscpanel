<?php
using('DAO', 'Core');
using('Query', 'Database');


/**
 * Provides a data layer abstraction to Permission Flags.
 *
 * @package    GameServerControlPanel
 * @subpackage App.DAO
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class PermissionFlags extends DAO
{
    //TODO: Document File  
    private static $_nameFlags = array ();
	private static $_isLoaded = false;

	private static function _LoadFlags()
	{
		$query = new Query();
		$query->Select('*', 'NamedPermissionFlags NPF');

		$resultset = self::Database()->Get($query);

		while($row = $resultset->fetch())
		{
			self::SetFlagValue($row['flag_name'], $row['flag_mask']);
		}

		self::$_isLoaded = true;
	}
    public static function SetFlagValue($flagName, $mask) 
    {
        self::$_nameFlags[$flagName] = $mask;
    }
    public static function GetFlagValue($flagName) 
    {
		if(!self::$_isLoaded)
			self::_LoadFlags();

        if ( !array_key_exists( $flagName, self::$_nameFlags ) )
            throw new UnknownPermissionFlagException( "No such user permission flag($flagName) exists..." );
        
        return self::$_nameFlags[$flagName];
    }
}