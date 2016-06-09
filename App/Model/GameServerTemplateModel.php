<?php
/**
 * Models game server templates on the system.
 *
 * @package    GameServerControlPanel
 * @subpackage App.Model
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class GameServerTemplateModel
{
	private $id;
	private $long_name;
	private $short_name;
	private $min_slots;
	private $max_slots;
	private $default_slots;
	private $default_cmd;
	private $game_files_zip;
	private $connection_url;
	private $default_port;

	public function __construct($id, $long_name, $short_name, $min_slots, $max_slots, $default_slots, $default_cmd, $game_files_zip, $connection_url, $default_port)
	{
		$this->id = $id;
		$this->long_name = $long_name;
		$this->short_name = $short_name;
		$this->min_slots = $min_slots;
		$this->max_slots = $max_slots;
		$this->default_slots = $default_slots;
		$this->default_cmd = $default_cmd;
		$this->game_files_zip = $game_files_zip;
		$this->connection_url = $connection_url;
		$this->default_port = $default_port;
	}
	public function GetId()
	{
		return $this->id;
	}
	public function GetName()
	{
		return $this->long_name;
	}
	public function GetMinSlots()
	{
		return $this->min_slots;
	}
	public function GetMaxSlots()
	{
		return $this->max_slots;
	}
	public function GetDefaultSlots()
	{
		return $this->default_slots;
	}
	public function GetDefaultPort()
	{
		return $this->default_port;
	}
	public function GetDefaultCMD()
	{
		return	 $this->default_cmd;
	}

	public function GetConnectionUrl()
	{
		return $this->connection_url;
	}
	public function GetZipFilename()
	{
		return $this->game_files_zip;
	}
}
