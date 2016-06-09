<?php
using('DAO', 'Core');
using('Query', 'Database');
using('GameServerTemplateModel', 'App.Model');

/**
 * Provides a data layer abstraction to Game Server Templatess.
 *
 * @package    GameServerControlPanel
 * @subpackage App.DAO
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class GameServerTemplate extends DAO
{
    public static function GetById($id)
    {
        $query = (new Query())->Select('*', 'GameServer_Template GST')
            ->Where('GST.template_id', $id);

        return self::Database()->Get($query);
    }

    public static function GetAllNameIds()
    {
        $query = (new Query())->Select(array(
            'GST.template_id',
            'GST.long_name'
        ), 'GameServer_Template GST');

        return self::Database()->Get($query);
    }

    public static function PopulateModel($row)
    {
        if ($row == false) return false;
        return new GameServerTemplateModel($row['template_id'], $row['long_name'], $row['short_name'], $row['min_slots'], $row['max_slots'], $row['default_slots'], $row['default_cmd'], $row['game_files_zip'], $row['connection_url'], $row['default_port']);
    }

    public static function GetModelById($id)
    {
        return self::PopulateModel(self::GetById($id)->fetch());
    }

    public static function GetAll()
    {
        $query = (new Query())->Select('*', 'GameServer_Template GST');

        return self::Database()->Get($query);
    }

    public static function Update($id, $name, $min_slots, $max_slots, $default_slots, $default_port, $default_cmd, $gamefiles, $connection_url)
    {
        $query = new Query();
        $query->Update('GameServer_Template', array(
            'long_name' => $name,
            'min_slots' => $min_slots,
            'max_slots' => $max_slots,
            'default_slots' => $default_slots,
            'default_port' => $default_port,
            'default_cmd' => $default_cmd,
            'game_files_zip' => $gamefiles,
            'connection_url' => $connection_url
        ))
            ->Where('template_id', $id);
        die($query);
        self::Database()->Exec($query);
    }

    public static function Insert($name, $min_slots, $max_slots, $default_slots, $default_port, $default_cmd, $gamefiles, $connection_url)
    {
        $query = new Query();
        $query->Insert('GameServer_Template', array(
            'template_id' => '',
            'long_name' => $name,
            'min_slots' => $min_slots,
            'max_slots' => $max_slots,
            'default_slots' => $default_slots,
            'default_port' => $default_port,
            'default_cmd' => $default_cmd,
            'game_files_zip' => $gamefiles,
            'connection_url' => $connection_url
        ));

        self::Database()->Exec($query);
        return self::Database()->PDO()->lastInsertId();
    }

    public static function Delete($id)
    {
        $query = new Query();
        $query->Delete('GameServer_Template')
            ->Where('template_id', $id);

        self::Database()->Exec($query);
    }

}