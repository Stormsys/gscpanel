<?php

/**
 * Exception Class to represent a Class Missing, usually during an autoload...
 *
 * @package    GameServerControlPanel
 * @subpackage System.Error
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 * */
class MissingClassException extends Exception
{
    //TODO: Document File         
    private $_filePath = "";
    private $_fileFound = false;

    public function __construct($className, $filePath, $fileFound = false, $code = 0, Exception $previous = null)
    {

        $this->_fileFound = $fileFound;
        $this->_filePath = $filePath;

        $message = "Unable to load class {$className}.";

        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": {$this->message}\n";
    }

    public function WasFileFound()
    {
        return $this->_fileFound;
    }

    public function GetFilePath()
    {
        return $this->_filePath;
    }
}