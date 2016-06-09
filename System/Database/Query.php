<?php
using('Query_Join', 'Database');
using('Query_Where', 'Database');
using('Query_Order', 'Database');

/**
 * TODO: Update Description
 *
 * @package    GameServerControlPanel
 * @subpackage System.Database
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Query
{
    const MODE_UNKNOWN = 0;
    const MODE_SELECT = 1;
    const MODE_UPDATE = 2;
    const MODE_INSERT = 3;
    const MODE_DELETE = 4;

    private $_select = '';
    private $_update = '';
    private $_insert = '';
    private $_delete = '';

    private $_join = array();
    private $_where = null;
    private $_order_by = array();


    private $_limit_count = 0;
    private $_limit_start = 0;


    private $_forced_mode = false;
    private $_mode = self::MODE_UNKNOWN;

    public function __construct()
    {

    }

    public function Delete($table)
    {
        $this->_delete = "DELETE FROM $table";
        return $this;
    }

    public function Select($values, $table)
    {
        $this->_select = 'SELECT ';

        if (is_array($values)) {
            $this->_select .= implode(',', $values);
        } else {
            $this->_select .= $values;
        }

        $this->_select .= " FROM $table";

        return $this;
    }

    public function Update($table, $values)
    {
        $this->_update = 'UPDATE ' . $table . ' SET ';

        $kvs = array();

        foreach ($values as $key => $value) {
            $kvs[] = "$key=" . GSCP_Core()->Database()->PDO()->quote($value);
        }

        $this->_update .= implode(', ', $kvs);

        return $this;
    }


    public function Insert($table, $values)
    {
        $this->_insert = 'INSERT INTO ' . $table;

        $this->_insert .= '(' . implode(', ', array_keys($values)) . ')';

        $insert_vals = array_values($values);
        foreach ($insert_vals as &$val) {
            if (!in_array(strtolower($val), array('date()', 'now()')))
                $val = GSCP_Core()->Database()->PDO()->quote($val);
        }

        $this->_insert .= ' VALUES(' . implode(', ', $insert_vals) . ')';

        return $this;
    }

    public function Join($table, $condition = '', $mode = 'INNER')
    {
        $this->_join[] = $join = new Query_Join($table, $mode);
        if (!empty($condition)) {
            $join->On($condition);
        }
        return $this;
    }

    public function On_NE($key, $value = '', $cond_mode = '=', $link_mode = 'AND')
    {
        $this->last_join()->On($key, $value, $cond_mode, $link_mode, false);
        return $this;
    }

    public function On($key, $value = '', $cond_mode = '=', $link_mode = 'AND')
    {
        $this->last_join()->On($key, $value, $cond_mode, $link_mode);
        return $this;
    }

    public function last_join()
    {
        return $this->_join[count($this->_join) - 1];
    }

    public function Where($key, $value = '', $cond_mode = '=', $link_mode = 'AND')
    {
        if (!isset($this->_where))
            $this->_where = new Query_Where();

        $this->_where->Where($key, $value, $cond_mode, $link_mode);

        return $this;
    }

    public function WhereNE($key, $value = '', $cond_mode = '=', $link_mode = 'AND')
    {
        if (!isset($this->_where))
            $this->_where = new Query_Where();

        $this->_where->Where($key, $value, $cond_mode, $link_mode, false);

        return $this;
    }

    public function OrderBy($field, $mode = 'ASC')
    {
        $this->_order_by[] = new Query_Order($field, $mode);

        return $this;
    }

    private function _ConstructOrder()
    {
        if (empty($this->_order_by)) {
            return null;
        }

        $query = array();

        foreach ($this->_order_by as $order) {
            $query[] = $order;
        }
        return 'ORDER BY ' . implode(', ', $query);
    }

    public function Construct()
    {
        $query = array();
        if (($this->_mode == self::MODE_UNKNOWN && isset($this->_select) && !empty($this->_select)) || $this->_mode == self::MODE_SELECT) {
            $query[] = $this->_select;
        } elseif (($this->_mode == self::MODE_UNKNOWN && isset($this->_update) && !empty($this->_update)) || $this->_mode == self::MODE_UPDATE) {
            $query[] = $this->_update;
        } elseif (($this->_mode == self::MODE_UNKNOWN && isset($this->_insert) && !empty($this->_insert)) || $this->_mode == self::MODE_INSERT) {
            $query[] = $this->_insert;
        } elseif (($this->_mode == self::MODE_UNKNOWN && isset($this->_delete) && !empty($this->_delete)) || $this->_mode == self::MODE_DELETE) {
            $query[] = $this->_delete;
        }


        foreach ($this->_join as $join) {
            $query[] = $join;
        }


        $query[] = $this->_where;

        $query[] = $this->_ConstructOrder();

        if ($this->_limit_count != 0)
            $query[] = "LIMIT {$this->_limit_start}, {$this->_limit_count}";

        return implode(' ', $query);
    }

    public function __toString()
    {
        return $this->Construct();
    }
}