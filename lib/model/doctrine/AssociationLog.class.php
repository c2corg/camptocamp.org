<?php
/*
 * $Id:$
 */
class AssociationLog extends BaseAssociationLog
{

    /**
     * Retrieves a pager of recent associations 
     * @param string model name
     * @return Pager
     */
    public static function listRecentChangesPager($doc_id = null, $users = null, $orderby = null, $npp = 25)
    {
        // TODO: possibility to filter on association type?

        $pager = new sfDoctrinePager('AssociationLog', $npp);

        $q = $pager->getQuery();
        $q->select('al.*, mi.name, mi.search_name, li.name, li.search_name, u.username, u.login_name, u.topo_name')
          ->from('AssociationLog al')
          ->leftJoin('al.mainI18n mi')
          ->leftJoin('al.linkedI18n li')
          ->leftJoin('al.user_private_data u');

        // filter on a specific doc if needed
        if ($doc_id)
        {
            $q->addWhere('al.main_id=? OR al.linked_id=?', array($doc_id, $doc_id));
        }

        // filter on a specific user if needed
        if ($users)
        {
            $users = explode('-', $users);
            if (count($users))
            {
                $where_ids = array();
                foreach ($users as $uid)
                {
                    $where_ids[] = '?';
                }
                $where_ids = implode(', ', $where_ids);
                if (count($where_ids) == 1)
                {
                    $where = ' = ' . $where_ids;
                }
                else
                {
                    $where = ' IN ( ' . $where_ids . ' )';
                }
                $q->addWhere('u.id' . $where, $users);
            }
        }
        
        if (empty($orderby))
        {
            $q->orderBy('al.associations_log_id DESC'); // ~ decreasing time (but faster, since there is an index on this field).
        }
        elseif ($orderby == 'uid')
        {
            $q->orderBy('u.id ASC');
        }

        return $pager;
    }
}
