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
class Query_Where
{
    private $_where = array();

    public function Where_Group($key, $cond_mode = '=', $link_mode = 'AND')
    {
        $this->Where($key, '', $cond_mode, $link_mode);
    }
    public function Where($key, $value = '', $cond_mode = '=', $link_mode = 'AND', $escape = true)
    {
        if ( !is_array($key) && empty($value) )
        {
            $this->_where[] = array(
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
                $this->_where[] = array(
                    'value' => "$k $cond_mode $v",
                    'mode'  => $link_mode
                );
            }
        }
        else
        {
            if($escape)
                $value = GSCP_Core()->Database()->PDO()->quote($value);
            $this->_where[] = array(
                'value' => "$key $cond_mode $value",
                'mode'  => $link_mode
            );
        }
    }
    public function Where_Or($key, $value = '', $cond_mode = '=')
    {
        $this->Where($key, $value, $cond_mode, 'OR');
    }
    public function __toString()
    {
        $where = "WHERE ";
        if( count($this->_where) > 0)
        {
            $first = true;
            foreach( $this->_where as $conds )
            {
                if($first)
                {
                    $first = false;
                }else
                    $where .= " {$conds['mode']} ";

                $where .= $conds['value'];
            }
        }

        return $where;
    }
}
