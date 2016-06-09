<?php

/**
 * Exception Class to represent an Unknown Permission Flag.
 *
 * @package    GameServerControlPanel
 * @subpackage System.Error
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 * */
class SSHConnectionFailedException extends Exception
{
    //TODO: Fix file.    
    public function __construct($host, $port, $code = 0, Exception $previous = null)
    {
        $message = "Unable to connect to $host:$port.";
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": {$this->message}\n";
    }
} 