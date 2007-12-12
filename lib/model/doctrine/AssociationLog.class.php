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
    public static function listRecentChangesPager()
    {
        // TODO: filter on association type

        $pager = new sfDoctrinePager('AssociationLog', sfConfig::get('app_list_maxline_number', 25));

        $q = $pager->getQuery();
        $q->select('al.*, mi.name, li.name, u.name_to_use, u.username, u.login_name, u.private_name')
          ->from('AssociationLog al')
          ->leftJoin('al.mainI18n mi')
          ->leftJoin('al.linkedI18n li')
          ->leftJoin('al.user_private_data u');
        
        //$q->where($query_params['query'], $query_params['arguments']);
        $q->orderBy('al.associations_log_id DESC'); // ~ decreasing time (but faster, since there is an index on this field).

        return $pager;
    }



}
