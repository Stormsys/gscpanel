<?php
using('MySql', 'Core');
using('Core', 'Core');

/**
 * TODO: Update Description
 *
 * @package    GameServerControlPanel
 * @subpackage System.Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Session
{
    private $session_id = null;
    private $user_agent = null;
    private $ip = null;
    private $data = null;
    private $cookie_name = 'gscp_session';
    private $dbname = 'Session';

    public function __construct($match_ip = false, $match_user_agent = false)
    {
        if (!(isset($_COOKIE[$this->cookie_name]) && !empty($_COOKIE[$this->cookie_name]) && $this->_Load($_COOKIE[$this->cookie_name])))
            $this->_GenerateNewSession();
    }

    public function Set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function Get($key = null)
    {
        if (empty($key))
            return $this->data;
        else
            return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    private function _Load($session_id)
    {
        $query = "SELECT session_id, ip, user_agent, data FROM {$this->dbname} WHERE session_id = ?";
        $result = GSCP_Core()->Database()->GetFirst($query, array($session_id));
        if ($result != null) {
            $this->session_id = $result['session_id'];
            $this->user_agent = $result['user_agent'];
            $this->ip = $result['ip'];
            $this->data = unserialize($result['data']);
            return true;
        }
        return false;
    }

    public function Save()
    {
        $query = "UPDATE {$this->dbname} SET ip = ?, user_agent = ?, data = ? WHERE session_id = ?";

        GSCP_Core()->Database()->Exec($query, array(
            $this->ip,
            $this->user_agent,
            serialize($this->data),
            $this->session_id
        ));
    }

    private function _StartInDB()
    {
        $query = "INSERT INTO {$this->dbname}(session_id, ip, user_agent, data) VALUES(?, ?, ?, ?)";

        GSCP_Core()->Database()->Exec($query, array(
            $this->session_id,
            $this->ip,
            $this->user_agent,
            serialize($this->data)
        ));

    }

    private function _GenerateNewSession()
    {
        $this->session_id = md5(uniqid(microtime()) . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->user_agent = $_SERVER['HTTP_USER_AGENT'];

        $this->_StartInDB();

        setcookie($this->cookie_name, $this->session_id, time() + (60 * 60 * 24), '/');
    }
}