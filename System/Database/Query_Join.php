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
class Query_Join
{
    private $_type = '';
    private $_table = '';

    private $_on = array();


    public function __construct($table, $type = 'INNER')
    {
        $this->_table = $table;
        $this->_type = $type;
    }
    public function On_Group($key, $cond_mode = '=', $link_mode = 'AND')
    {
        $this->On($key, '', $cond_mode, $link_mode);
    }
    public function On($key, $value = '', $cond_mode = '=', $link_mode = 'AND', $escape = true)
    {
        if ( !is_array($key) && empty($value) )
        {
            $this->_on[] = array(
                'value' => $key,
                'mode'  => $link_mode
            );
        }
        else if ( is_array($key) )
        {
            foreach( $key as $k => $v )
            {
                if($escape)
                    $v = GSCP_Core()->Database()->PDO()->quote($v);
                $this->_on[] = array(
                    'value' => "$k $cond_mode $v",
                    'mode'  => $link_mode
                );
            }
        }
        else
        {
            if($escape)
               $value = GSCP_Core()->Database()->PDO()->quote($value);
            $this->_on[] = array(
                'value' => "$key $cond_mode $value",
                'mode'  => $link_mode
            );
        }
    }
    public function On_Or($key, $value = '', $cond_mode = '=')
    {
        $this->On($key, $value, $cond_mode, 'OR');
    }
    public function __toString()
    {
        $join = "{$this->_type} JOIN {$this->_table}";
        if( count($this->_on) > 0)
        {
            $first = true;
            foreach( $this->_on as $on )
            {
                if($first)
                {
                    $join .= ' ON ';
                    $first = false;
                }else
                    $join .= " {$on['mode']} ";

                $join .= $on['value'];
            }
        }

        return $join;
    }
}
