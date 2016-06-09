<?php
using('DAO', 'Core');
using('Query', 'Database');
using('GameServerStatus', 'App.Model');
using('GameServerModel', 'App.Model');

/**
 * Provides a data layer abstraction to Game Servers.
 *
 * @package    GameServerControlPanel
 * @subpackage App.DAO
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class GameServer extends DAO
{

	public static function SetServerStatus($gsid, $status)
	{
		if(!in_array($status, array(GameServerStatus::OFFLINE, GameServerStatus::ONLINE, GameServerStatus::RESTARTING, GameServerStatus::PENDING, GameServerStatus::INSTALLING)))
			throw new Exception('Invalid Status Specified');

		$query = new Query();
		$query->Update('GameServers', array(
			'online_status' => $status
		))
		->Where('gs_id', $gsid);

		self::Database()->Exec($query);
	}
	public static function GetAllIds()
	{
		$query = new Query();
		$query->Select('GS.gs_id', 'GameServers GS');

		return self::Database()->Get($query);
	}
	public static function GetModelById($id)
	{
		return self::PopulateModel(self::GetById($id)->fetch());
	}
	public static function GetById($id)
	{
		$query = new Query();
		$query->Select('*', 'GameServers GS')
			->Where('GS.gs_id', $id);

		return self::Database()->Get($query);
	}
	public static function Delete($id)
	{
		$query = new Query();
		$query->Delete('GameServers')
			->Where('gs_id', $id);

		return self::Database()->Get($query);
	}
	public static function PopulateModel($r)
	{
		return new GameServerModel($r['gs_id'], $r['dedserver_id'], $r['template_id'], $r['owner_id'], $r['nickname'], $r['ip'], $r['port'], $r['paramater_overide'],
									$r['slot_count'], $r['ftp_username'], $r['ftp_password'], $r['online_status'], $r['server_dir']);
	}
	public static function GetServersForUser($user_id)
	{

		$query = new Query();
		$query->Select(
			array(
				'GS.*',
				'GST.long_name \'game_name\''
			),
			'GameServers GS')
			->Join('Users U')
			->On_NE('U.user_id', 'GS.owner_id')
			->Join('GameServer_Template GST')
			->On_NE('GST.template_id', 'GS.template_id')
			->Where('GS.owner_id', $user_id);

		return self::Database()->Get($query);
	}
	public static function GetServersStatusForUser($user_id)
	{

		$query = new Query();
		$query->Select(
			array(
				'GS.gs_id',
				'GS.online_status',
				'GS.nickname'
			),
			'GameServers GS')
			->Join('Users U')
			->On_NE('U.user_id', 'GS.owner_id')
			->Where('GS.owner_id', $user_id);

		return self::Database()->Get($query);
	}
	public static function CountServers($offset, $limit)
	{
	}
	public static function GetServers($offset, $limit)
	{

		$query = new Query();
		$query->Select(
			array(
				'GS.*',
				'GST.long_name \'gst_type\'',
				'GST.template_id \'gst_id\'',
				'U.username \'user_username\'',
				'CONCAT(U.first_name, \' \', U.last_name) \'user_name\'',
				'U.user_id \'user_id\'',
				'DS.dserver_id \'ds_id\'',
				'DS.nickname \'ds_nickname\''
			),
			'GameServers GS')
			->Join('Users U','','LEFT')
			->On_NE('U.user_id', 'GS.owner_id')
			->Join('GameServer_Template GST','','LEFT')
			->On_NE('GST.template_id', 'GS.template_id')
			->Join('DedicatedServers DS','','LEFT')
			->On_NE('DS.dserver_id', 'GS.dedserver_id');

		return self::Database()->Get($query);
	}


	public static function Update($id, $dsid,  $tid, $oid, $nickname, $ip, $port, $slots, $cmd, $ftp_pass)
	{
		$query = new Query();
		$query->Update('GameServers', array(
			'dedserver_id' => $dsid,
			'template_id' => $tid,
			'owner_id' => $oid,
			'nickname' => $nickname,
			'ip' => $ip,
			'port' => $port,
			'paramater_overide' => $cmd,
			'slot_count' => $slots,
			'ftp_password' => $ftp_pass
		))
		->Where('gs_id', $id);
		self::Database()->Exec($query);
	}

	public static function SoftUpdate($id, $dir, $ftp_user, $ftp_pass)
	{
		$query = new Query();
		$query->Update('GameServers', array(
			'server_dir' => $dir,
			'ftp_username' => $ftp_user,
			'ftp_password' => $ftp_pass
		))
		->Where('gs_id', $id);
		self::Database()->Exec($query);
	}
	public static function Insert($dsid,  $tid, $oid, $nickname, $ip, $port, $slots, $cmd, $dir, $ftp_user, $ftp_pass)
	{
		$query = new Query();
		$query->Insert('GameServers', array(
			'dedserver_id' => $dsid,
			'template_id' => $tid,
			'owner_id' => $oid,
			'nickname' => $nickname,
			'ip' => $ip,
			'port' => $port,
			'paramater_overide' => $cmd,
			'server_dir' => $dir,
			'slot_count' => $slots,
			'ftp_username' => $ftp_user,
			'ftp_password' => $ftp_pass
		));
		self::Database()->Exec($query);
		return self::Database()->PDO()->lastInsertId();
	}
}