<?php

/**
 * PlaceHolder for missing files Exception
 *
 * @package    GameServerControlPanel
 * @subpackage System.Error
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class FileNotFoundException extends Exception
{
    protected $missingFileLoc;

    public function __construct($message = null, $missingFile = null, $code = 0, Exception $previous = null)
    {
        $this->missingFileLoc = $missingFile;

        parent::__construct($message . " \nMissing File: " . $missingFile, $code, $previous);
    }
} 