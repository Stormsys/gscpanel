<?php
using('DAO', 'Core');
using('Query', 'Database');
using('DedicatedServerModel', 'App.Model');

/**
 * Provides a data layer abstraction to Dedicated Servers.
 *
 * @package    GameServerControlPanel
 * @subpackage App.DAO
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class DedicatedServer extends DAO
{
    /**
     * Returns a PDO object of the requested dedicated server.
     *
     * @param int $id the id of the dedicated server.
     */
    public static function GetById($id)
    {
        $query = (new Query())->Select('*', 'DedicatedServers DS')
            ->Where('DS.dserver_id', $id);

        return self::Database()->Get($query);
    }

    /**
     * Returns a PDO object of the requested dedicated server.
     *
     * @param int $id the id of the dedicated server.
     */
    public static function GetIp($id)
    {
        $query = (new Query())->Select('DS.main_ip', 'DedicatedServers DS')
            ->Where('DS.dserver_id', $id);
        $result = self::Database()->Get($query)->fetch();
        return $result['main_ip'];
    }

    /**
     * Populates a model with the dedicated server details.
     *
     * @param result $row a result row from the database.
     */
    public static function PopulateModel($row)
    {
        if ($row == false) return false;
        return new DedicatedServerModel($row['dserver_id'], $row['main_ip'], $row['ssh_ip'], $row['ssh_port'], $row['ssh_username'], $row['ssh_password'], $row['installs_dir'], $row['nickname']);
    }


    /**
     * Returns a model of the requested dedicated server.
     *
     * @param int $id the id of the dedicated server.
     */
    public static function GetModelById($id)
    {
        return self::PopulateModel(self::GetById($id)->fetch());
    }


    /**
     * Returns all dedicated servers on the system.
     */
    public static function GetAll()
    {
        $query = (new Query())->Select('*', 'DedicatedServers DS');

        return self::Database()->Get($query);
    }


    /**
     * Gets a list of ids and names.
     */
    public static function GetAllNameIds()
    {
        $query = (new Query())->Select(array(
            'DS.dserver_id',
            'DS.nickname'
        ), 'DedicatedServers DS');

        return self::Database()->Get($query);
    }

    /**
     * Updates a dedicated server on the system.
     *
     * @param int $dsid the id of the dedicated server.
     * @param string $main_ip the main ip of the dedicated server.
     * @param string $nick the nickname of the dedicated server.
     * @param string $ssh_ip the ssh connection ip of the dedicated server.
     * @param int $ssh_port the port for ssh connections on the deidcated server.
     * @param string $ssh_user the username of the root user on the server.
     * @param string $ssh_pass the password of the root user on the server.
     * @param string $install_dir the installation directory of the server.
     */
    public static function Update($dsid, $main_ip, $nick, $ssh_ip, $ssh_port, $ssh_user, $ssh_pass, $install_dir)
    {
        $query = new Query();
        $query->Update('DedicatedServers', array(
            'main_ip' => $main_ip,
            'nickname' => $nick,
            'ssh_ip' => $ssh_ip,
            'ssh_port' => $ssh_port,
            'ssh_username' => $ssh_user,
            'ssh_password' => $ssh_pass,
            'installs_dir' => $install_dir
        ))
            ->Where('dserver_id', $dsid);

        self::Database()->Exec($query);
    }


    /**
     * Updates a dedicated server on the system.
     *
     * @param string $main_ip the main ip of the dedicated server.
     * @param string $nick the nickname of the dedicated server.
     * @param string $ssh_ip the ssh connection ip of the dedicated server.
     * @param int $ssh_port the port for ssh connections on the deidcated server.
     * @param string $ssh_user the username of the root user on the server.
     * @param string $ssh_pass the password of the root user on the server.
     * @param string $install_dir the installation directory of the server.
     * @return int id of the new dedicated server.
     */
    public static function Insert($main_ip, $nick, $ssh_ip, $ssh_port, $ssh_user, $ssh_pass, $install_dir)
    {
        $query = new Query();
        $query->Insert('DedicatedServers', array(
            'dserver_id' => '',
            'main_ip' => $main_ip,
            'nickname' => $nick,
            'ssh_ip' => $ssh_ip,
            'ssh_port' => $ssh_port,
            'ssh_username' => $ssh_user,
            'ssh_password' => $ssh_pass,
            'installs_dir' => $install_dir
        ));

        self::Database()->Exec($query);
        return self::Database()->PDO()->lastInsertId();
    }


    /**
     * Deletes a server from the system.
     *
     * @param int $dsid the id of the dedicated server.
     */
    public static function Delete($dsid)
    {
        $query = new Query();
        $query->Delete('DedicatedServers')
            ->Where('dserver_id', $dsid);

        self::Database()->Exec($query);
    }
}