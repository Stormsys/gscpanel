<?php
using('DAO', 'Core');
using('Query', 'Database');
using('UserModel', 'App.Model');

/**
 * Provides a data layer abstraction to Users.
 *
 * @package    GameServerControlPanel
 * @subpackage App.DAO
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class User extends DAO
{
	public static function GetAll()
	{
		$query = (new Query())->Select('*', 'Users U');

		return self::Database()->Get($query);
	}
	public static function Insert($username, $firstname, $lastname, $email, $password)
	{
		$query = new Query();
		$query->Insert('Users', array(
			'username' 	=> $username,
			'first_name' 	=> $firstname,
			'first_name' 	=> $firstname,
			'last_name' 	=> $lastname,
			'email' 		=> $email,
			'password'		=> $password
		));

		self::Database()->Exec($query);
		return self::Database()->PDO()->lastInsertId();
	}
	public static function BasicUpdate($id, $firstname, $lastname, $email)
	{
		$query = (new Query())->Update('Users', array(
			'first_name' 	=> $firstname,
			'last_name' 	=> $lastname,
			'email' 		=> $email
		))
			->Where('user_id', $id);

		return self::Database()->Exec($query);
	}

	public static function BasicUpdateAdmin($id,$username, $firstname, $lastname, $email)
	{
		$query = (new Query())->Update('Users', array(
			'username' 	=> $username,
			'first_name' 	=> $firstname,
			'last_name' 	=> $lastname,
			'email' 		=> $email
		))
		->Where('user_id', $id);

		return self::Database()->Exec($query);
	}
	public static function ChangePassword($id, $password)
	{
		$query = (new Query())->Update('Users', array(
			'password' 	=> $password
		))
			->Where('user_id', $id);

		return self::Database()->Exec($query);
	}
	public static function GetById($id)
	{
		$query = (new Query())->Select('*', 'Users U')
			->Where('U.user_id', $id);

		return self::Database()->Get($query);
	}
	public static function GetAllNameIds()
	{
		$query = (new Query())->Select(array(
			'U.user_id',
			'U.username',
			'U.first_name',
			'U.last_name'
		), 'Users U');

		return self::Database()->Get($query);
	}
	public static function GetByLogin($username, $password)
    {
		$query = (new Query())->Select('*', 'Users U')
			->Where('U.username', $username)
			->Where('U.password', $password);

		return self::Database()->Get($query);
	}
	public static function PopulateModel($row)
	{
		if($row == false) return false;
		return new UserModel($row['user_id'], $row['username'], $row['password'], $row['permissions_overide'], $row['first_name'], $row['last_name'], $row['email']);
	}

	public static function GetModelById($id)
	{
		return self::PopulateModel(self::GetById($id)->fetch());
	}
	public static function GetModelByLogin($username, $passsword)
	{
		return self::PopulateModel(self::GetByLogin($username, $passsword)->fetch());
	}
	public static function Delete($id)
	{
		$query = new Query();
		$query->Delete('Users')
			->Where('user_id', $id);

		self::Database()->Exec($query);
	}
}