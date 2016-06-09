<?php
using('Core', 'Core');

/**
 * TODO: Update Description
 *
 * @package    GameServerControlPanel
 * @subpackage System.Database
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Query_Order
{
	private $_field = '';
	private $_mode = 'ASC';


	public function __construct($field, $mode = 'ASC')
	{
		if(!in_array(strtolower($mode), array('asc', 'desc')))
			throw new Exception('Invalid Mode Selected for Order');

		$this->_field = $field;
		$this->_mode = $mode;
	}
	public function __toString()
	{
		return $this->_field . ' ' . $this->_mode;
	}
}
