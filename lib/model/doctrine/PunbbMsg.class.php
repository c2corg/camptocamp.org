<?php
/**
 * $Id: PunbbMsg.class.php 1019 2007-07-23 18:36:35Z alex $
 */

class PunbbMsg extends BasePunbbMsg
{
    public static function GetUnreadMsg($userId)
    {
        return Doctrine_Query::create()
                             ->select('COUNT(u.id) num_posts')
                             ->from('PunbbMsg u')
                             ->where('u.owner = ? AND u.showed = 0', array($userId))
                             ->execute()
                             ->getFirst()->num_posts; 
    }
}
