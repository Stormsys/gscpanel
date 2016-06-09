<?php
using('PermissionFlags', 'App.DAO');

/**
 * User Permission Class.
 * 
 * @package    GameServerControlPanel  
 * @subpackage App.Model
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 * */
class PermissionsModel 
{
	private $_userMask = 0;
    
    public function __construct($mask)
    {
        $this->SetMask($mask);
    }
      
    /**
     * Sets the user permission mask to the value.
     *
     * @param  int $mask the mask that you want to set.
     * @return void                           
     */  
	public function SetMask($mask) 
    {
		$this->_userMask = $mask;
	}
    
    /**
     * takes in a flag mask and performs an | operation on it, effectivly granting the permissions of both..
     *
     * @param  int $flagValue the flag mask that you want to add.
     * @return void                           
     */
	public function AddFlag($flagValue) 
    {
		$this->_userMask |= $flagValue;
	}
         
    /**
     * Adds a list of flags, by name, to the current permission set.
     *
     * @param  string ... list of flagNames to add to the permissions .
     * @return void
     * @throws UnknownPermissionFlagException
     */
    public function Add() 
    {
		foreach ( func_get_args () as $flagName ) {
			$this->AddFlag( $this->_permissionFlags->GetFlagValue( $flagName ) );
		}                
	}
	
	/**
	 * Check's whether a list of permission flags are present in the current
	 * UserPermission instance.
	 *
	 * @param  string ... list of flagNames to check, all must return true.
	 * @return boolean
     * @throws UnknownPermissionFlagException
	 */
	public function Has() 
    {
		foreach ( func_get_args () as $flagName ) {
			if ( !( $this->_userMask & PermissionFlags::GetFlagValue( $flagName ) ) )
				return false;
		}
		return true;
	}
} 