<?php

/**
 * TODO: Update Description
 *
 * @package    GameServerControlPanel
 * @subpackage System.Database
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Database
{
    private $_pdo = null;
    private $_tbl_prefix = '';

    public function __construct($hostname, $username, $password, $dbname, $tbl_prefix = '')
    {
        $this->_tbl_prefix = $tbl_prefix;
        $this->_pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
        $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function Exec($query, $data = null)
    {
        $query = $this->_pdo->prepare($query);
        $query->execute($data);

        unset($query);
    }

    public function GetFirst($query, $data = null)
    {
        $query = $this->_pdo->prepare($query);
        $query->execute($data);

        $query->setFetchMode(PDO::FETCH_ASSOC);

        return $query->fetch();
    }

    public function Get($query, $data = null)
    {
        $query = $this->_pdo->prepare($query);
        $query->execute($data);

        $query->setFetchMode(PDO::FETCH_ASSOC);

        return $query;
    }

    public function PDO()
    {
        return $this->_pdo;
    }
}