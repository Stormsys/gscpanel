<?php
using('DAO', 'Core');
using('Query', 'Database');

/**
 * Provides a data layer abstraction to the Queue.
 *
 * @package    GameServerControlPanel
 * @subpackage App.DAO
 * @author     Diogo Moura
 * @copyright  Copyright (c) 2012+, Diogo Moura
 * @version    1.0
 **/
class Queue extends DAO
{
    public static function GetNextItem()
    {
        $query = new Query();

        $query->Select('*', 'Queue Q')
            ->Where('Q.in_progress', '1', '!=')
            ->OrderBy('Q.date_added', 'ASC');

        return self::Database()->Get($query);
    }

    public static function DeleteAllInProgress()
    {
        $query = new Query();

        $query->Delete('Queue')
            ->Where('in_progress', '1');

        return self::Database()->Exec($query);
    }

    public static function AddAction($gs_id, $type, $data = null)
    {
        $query = new Query();
        $query->Insert('Queue', array(
            'action_id' => '',
            'type' => $type,
            'gs_id' => $gs_id,
            'data' => empty($data) ? null : serialize($data),
            'date_added' => 'NOW()'
        ));

        self::Database()->Exec($query);
    }

    public static function InProgress($actid)
    {
        $query = new Query();
        $query->Update('Queue Q', array(
            'Q.in_progress' => '1'
        ))
            ->Where('Q.action_id', $actid);

        self::Database()->Exec($query);
    }
}