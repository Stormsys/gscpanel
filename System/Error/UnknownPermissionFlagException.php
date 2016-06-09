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
class UnknownPermissionFlagException  extends Exception {         
    //TODO: Document File        
    public function __construct($flagName, $code = 0, Exception $previous = null) {     
        $message = "No such user permission flag($flagName) exists, have you instanciated the static class?";
        parent::__construct($message, $code, $previous);
    }                                                                  
    public function __toString() {
        return __CLASS__ . ": {$this->message}\n";
    }
}   