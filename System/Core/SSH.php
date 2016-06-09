<?php
using('SSHConnectionFailedException', 'Error');

/**
 * SSH Class.
 *
 * @package    GameServerControlPanel
 * @subpackage System.Core
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class SSH
{
    private $_connection = null;


    public function __construct($host, $port, $username, $password)
    {
        if (($this->_connection = ssh2_connect($host, $port)) == false)
            throw new SSHConnectionFailedException($host, $port);

        $fingerprint = ssh2_fingerprint($this->_connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);
        //  if (strcmp($this->ssh_server_fp, $fingerprint) !== 0) { 
        //     throw new Exception('Unable to verify server identity!'); 
        // }                          
        if (!ssh2_auth_password($this->_connection, $username, $password)) {
            throw new Exception('Authentication rejected by server');
        }
    }

    public function Run($cmd, &$error = "")
    {
        if (!($stream = ssh2_exec($this->_connection, $cmd)))
            throw new Exception('SSH command failed');

        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);


        stream_set_blocking($stream, true);
        stream_set_blocking($errorStream, true);

        $data = stream_get_contents($stream);
        $error = stream_get_contents($errorStream);

        return $data;
    }

    /**
     * Disconnects when unset(class)  happens
     */
    private function _Disconnect()
    {
        $this->Run('echo "EXITING" && exit;');
        $this->_connection = null;
    }

    public function __destruct()
    {
        $this->_Disconnect();
    }

}