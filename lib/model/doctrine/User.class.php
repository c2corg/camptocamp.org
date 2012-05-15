<?php
/**
 * $Id: User.class.php 2535 2007-12-19 18:26:27Z alex $
 */

class User extends BaseUser
{
    protected $allPermissions;
    
    public static function filterSetV4_id($value)
    {   
        return self::returnNullIfEmpty($value);
    }

    public static function filterSetActivities($value)
    {
        return self::convertArrayToString($value);
    }

    public static function filterGetActivities($value)
    {
        return self::convertStringToArray($value);
    }

    public static function filterSetCategory($value)
    {
        return self::returnNullIfEmpty($value);
    }

    public function __toString(){
        return $this->get('private_data')->getLoginName();
    }

    public function isConfirmationPending()
    {
        return $this->isInGroup(sfConfig::get('app_group_name_pending'));
    }

    public function isActive()
    {
        return $this->isInGroup(sfConfig::get('app_group_name_logged'));
    }

    public function removeFromGroup($group_name)
    {
        $user_group = UserGroup::findByUserIdAndGroupName($this->get('id'), $group_name);
        $user_group->delete();
    }

    protected function isInGroup($group_name)
    {
        $nb = Doctrine_Query::create()
                      ->select('COUNT(ug.group_id) nb')
                      ->from('UserGroup ug')
                      ->where('ug.group_id = (SELECT g.id FROM Group g WHERE g.name = ?)
                               AND ug.user_id = ?')
                      ->limit(1)
                      ->execute(array($group_name, $this->getId()))
                      ->getFirst()
                      ->nb;

        c2cTools::log('{user->isInGroup()} group name : ' .
                       $group_name . 'user id : ' . $this->getId() .
                       'result : ' . $nb);

        return ($nb > 0) ? true : false ;
    }

    public static function retrieve($username, $password)
    {
        return Doctrine_Query::create()
                             ->from('User')
                             ->where('User.private_data.login_name = ? AND User.private_data.password = ?',
                                     array($username, $password))
                             ->limit(1)
                             ->execute()
                             ->getFirst();
    }


    /**
     * add user to groups
     *
     * @param array $group_name
     */
    public function addToGroups($group_name)
    {
        $group_ids = Group::findGroupIdByNames($group_name);

        foreach ($group_ids as $group_id)
        {
            $user_group = new UserGroup();
            $user_group->user_id = $this->getId();
            $user_group->group_id = $group_id;
            $user_group->save();
        }
    }

    /**
     * Try to retrieve the user using his temporary password field
     *
     * @param string $username
     * @param string $password
     * @return user_or_doctrine_null_object
     */
    public static function retrieveTmp($username, $password)
    {
        return Doctrine_Query::create()
                             ->from('User')
                             ->where('User.private_data.login_name = ? AND User.private_data.password_tmp = ?',
                                     array(strtolower($username), $password))
                             ->limit(1)
                             ->execute()
                             ->getFirst();
    }

    public static function getOutOfDatePendingUserIds()
    {
        $max_pending_time = sfConfig::get('app_pending_users_lifetime');
        $limit_date = time() - ($max_pending_time * 24 * 60 * 60);

        $sql = 'SELECT a.id FROM app_users_private_data a, app_users_groups ug ' .
               'WHERE a.id = ug.user_id AND ug.group_id = 4 AND a.registered < ?';

        $rs = sfDoctrine::connection()
                        ->standaloneQuery($sql, array($limit_date))
                        ->fetchAll();

        $expired_users = array();
        if (count($rs) > 0)
        {
        	foreach ($rs as $item)
            {
                $expired_users[] = $item['id'];
            }
        }

        return $expired_users;
    }

    public function getAllPermissionNames()
    {
        return array_keys($this->getAllPermissions());
    }

    /**
     * Get all permissions, from user and related groups
     * Taken from the sfGuardplugin
     * TODO: make a single query...
     *
     * @return associated_array permission_name => permission_name
     */
    public function getAllPermissions()
    {
        if (!$this->allPermissions)
        {
            $this->allPermissions = array();

            $user = Doctrine_Query::create()->
                                  from('User.groups.permissions, User.permissions')->
                                  where('User.id = ?', $this->getId())->
                                  execute(array(), Doctrine::FETCH_ARRAY);

            // Get all the permissions from associated groups
            foreach ($user[0]['groups'] as $group)
            {
            	foreach ($group['permissions'] as $permission)
            	{
            		$this->allPermissions[$permission['name']] = $permission['name'];
            	}
            }

            // Get all the permissions from the user itself
            foreach($user[0]['permissions'] as $permission)
            {
                $this->allPermissions[$permission['name']] = $permission['name'];
            }
        }

        return $this->allPermissions;
    }

    public static function buildUserListCriteria(&$conditions, &$values, $params_list, $is_module = false)
    {
        if ($is_module)
        {
            $m = 'm';
            $m2 = 'u';
            $join = null;
            $join_id = null;
            $join_i18n = null;
            $join_private_data = null;
        }
        else
        {
            $m = 'u';
            $m2 = $m;
            $join = 'join_user';
            $join_id = 'join_user_id';
            $join_i18n = 'join_user_i18n';
            $join_private_data = 'join_user_pd';
        }
        
        if ($is_module)
        {
            $has_id = self::buildConditionItem($conditions, $values, 'List', 'm.id', 'id', $join_id, false, $params_list);
        }
        else
        {
            $has_id = self::buildConditionItem($conditions, $values, 'Multilist', array('lu', 'main_id'), 'users', $join_id, false, $params_list);
        }
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, 'Array', array($m, 'u', 'activities'), 'act', $join, false, $params_list);
                self::buildConditionItem($conditions, $values, 'Georef', $join, 'geom', $join, false, $params_list);
                self::buildConditionItem($conditions, $values, 'Mstring', array('mi.search_name', 'upd.search_username'), 'utfnam', null, false, $params_list);
            }
            
            // friends
            $user_groups = c2cTools::getArrayElement($params_list, 'friends');
            if (!is_null($user_groups))
            {
                $user_groups = explode(' ', $user_groups);
                $user_ids = array();
                $friend_ids = array();
                $first_group = true;
                foreach ($user_groups as $user_group)
                {
                    $user_group_ids = explode('-', $user_group);
                    $user_ids = array_merge($user_ids, $user_group_ids);
                    $conditions_temp = array("a.type = 'uo'");
                    $values_temp = array();
                    self::buildListCondition($conditions_temp, $values_temp, 'lu.main_id', $user_group);
                    $where = implode(' AND ', $conditions_temp);
                    
                    $friends = Doctrine_Query::create()
                     ->select('DISTINCT a.main_id')
                     ->from('Association a')
                     ->leftJoin('a.MainMainAssociation lu')
                     ->where($where, $values_temp)
                     ->execute(array(), Doctrine::FETCH_ARRAY);
                    
                    if (count($friends))
                    {
                        $friend_group_ids = array();
                        foreach ($friends as $friend)
                        {
                            $friend_group_ids[] = $friend['main_id'];
                        }
                        $friend_group_ids = array_unique($friend_group_ids);
                        if ($first_group)
                        {
                            $friend_ids = $friend_group_ids;
                        }
                        else
                        {
                            $friend_ids = array_intersect($friend_ids, $friend_group_ids);
                        }
                    }
                    
                    $first_group = false;
                }
                
                if (count($friend_ids))
                {
                    $friend_ids = array_diff($friend_ids, $user_ids);
                    $params_list['friends'] = implode('-', $friend_ids);
                    if ($is_module)
                    {
                        self::buildConditionItem($conditions, $values, 'List', 'm.id', 'friends', $join_id, false, $params_list);
                    }
                    else
                    {
                        self::buildConditionItem($conditions, $values, 'Multilist', array('lu', 'main_id'), 'friends', $join_id, false, $params_list);
                    }
                }
            }
            
            self::buildConditionItem($conditions, $values, 'Around', $m2 . '.geom', 'uarnd', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'String', $m . 'i.search_name', ($is_module ? array('unam', 'name') : 'unam'), $join_i18n, false, $params_list);
            self::buildConditionItem($conditions, $values, 'String', 'upd.search_username', 'ufnam', $join_private_data, false, $params_list);
            self::buildConditionItem($conditions, $values, 'Array', array($m, 'u', 'activities'), 'uact', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', $m . '.category', 'ucat', $join, false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'ui.culture', 'ucult', 'join_user_i18n', false, $params_list);
            self::buildConditionItem($conditions, $values, 'List', 'luc.linked_id', 'utags', 'join_utag_id', false, $params_list);
        }
    }
    
    public static function buildListCriteria($params_list)
    {
        $conditions = $values = array();

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $params_list, 'ucult');

        // criteria to hide users whithout public profile
        if (!sfContext::getInstance()->getUser()->isConnected())
        {
            $conditions[] = 'upd.is_profile_public IS TRUE';
        }
        
        // return if no criteria
        $citeria_temp = c2cTools::getCriteriaRequestParameters(array('perso'));
        if (isset($conditions['all']) || empty($citeria_temp))
        {
            return array($conditions, $values);
        }
        
        // area criteria
        self::buildAreaCriteria($conditions, $values, $params_list, 'u');
        self::buildConditionItem($conditions, $values, 'Multilist', array('go', 'linked_id'), 'oareas', 'join_oarea', false, $params_list);
        if (isset($conditions['join_oarea']))
        {
            $conditions['join_outing_id_has'] = true;
        }
        
        // user criteria
        User::buildUserListCriteria($conditions, $values, $params_list, true);
       
        // outing criteria
        Outing::buildOutingListCriteria($conditions, $values, $params_list, false, 'lo.linked_id');

        // route criteria
        Route::buildRouteListCriteria($conditions, $values, $params_list, false, 'lr.main_id');

        // summit criteria
        Summit::buildSummitListCriteria($conditions, $values, $params_list, false, 'ls.main_id');
        
        // image criteria
        Image::buildImageListCriteria($conditions, $values, $params_list, false, 'li.document_id');

        if (!empty($conditions))
        {
            return array($conditions, $values);
        }

        return array();
    }

    public static function browse($sort, $criteria, $format = null)
    {   
        $pager = self::createPager('User', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        self::joinOnRegions($q);
        $q->leftJoin('m.private_data upd');

        $conditions = array();
        $all = false;
        if (!empty($criteria))
        {
            $conditions = $criteria[0];
            if (isset($conditions['all']))
            {
                $all = $conditions['all'];
                unset($conditions['all']);
            }
        }
        
        if (!$all && !empty($conditions))
        {
            self::buildPagerConditions($q, $conditions, $criteria[1]);
        }
        elseif (!$all && c2cPersonalization::getInstance()->areFiltersActiveAndOn('users'))
        {
            self::filterOnActivities($q);
            self::filterOnRegions($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }
    
    public static function buildUserPagerConditions(&$q, &$conditions, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        if ($is_module)
        {
            $m = 'm.';
            $linked = '';
            $linked2 = '';
        }
        else
        {
            $m = 'lu.';
            if ($is_linked)
            {
                $linked = 'Linked';
                $linked2 = '';
            }
            else
            {
                $linked = '';
                $linked2 = 'Linked';
            }
                
            if (isset($conditions['join_user_id']))
            {
                $conditions = self::joinOnMulti($q, $conditions, 'join_user_id', $first_join . ' lu', 4);
                
                return;
            }
            else
            {
                $q->leftJoin($first_join . ' lu')
                  ->addWhere($m . "type = '$ltype'");
            }
            
            if (isset($conditions['join_user']))
            {
                $q->leftJoin($m . $linked . 'User u');
                unset($conditions['join_user']);
            }

            if (isset($conditions['join_user_pd']))
            {
                $q->leftJoin($m . $linked . 'UserPrivateData upd');
                unset($conditions['join_user_pd']);
            }
        }

        if (isset($conditions['join_user_i18n']))
        {
            $q->leftJoin($m . $linked . 'UserI18n ui');
            unset($conditions['join_user_i18n']);
        }
        
        if (isset($conditions['join_utag_id']))
        {
            $q->leftJoin($m . $linked2 . "LinkedAssociation luc");
            unset($conditions['join_utag_id']);
        }
    }
    
    public static function buildPagerConditions(&$q, &$conditions, $criteria)
    {
        $conditions = self::joinOnMultiRegions($q, $conditions);
        
        // join with users tables only if needed 
        if (   isset($conditions['join_user_id'])
            || isset($conditions['join_user_i18n'])
            || isset($conditions['join_utag_id'])
        )
        {
            User::buildUserPagerConditions($q, $conditions, true);
        }

        // join with outings tables only if needed 
        if (   isset($conditions['join_route_id'])
            || isset($conditions['join_route'])
            || isset($conditions['join_route_i18n'])
            || isset($conditions['join_rdoc_id'])
            || isset($conditions['join_rtag_id'])
            || isset($conditions['join_rdtag_id'])
            || isset($conditions['join_rbook_id'])
            || isset($conditions['join_rbtag_id'])
            || isset($conditions['join_summit_id'])
            || isset($conditions['join_summit'])
            || isset($conditions['join_summit_i18n'])
            || isset($conditions['join_stag_id'])
            || isset($conditions['join_sbook_id'])
            || isset($conditions['join_sbtag_id'])
            || isset($conditions['join_outing_id'])
            || isset($conditions['join_outing_id_has'])
            || isset($conditions['join_outing'])
            || isset($conditions['join_outing_i18n'])
            || isset($conditions['join_otag_id'])
        )
        {
            Outing::buildOutingPagerConditions($q, $conditions, false, true, 'm.LinkedAssociation', 'uo');
            

            if (   isset($conditions['join_route_id'])
                || isset($conditions['join_route'])
                || isset($conditions['join_route_i18n'])
                || isset($conditions['join_rdoc_id'])
                || isset($conditions['join_rtag_id'])
                || isset($conditions['join_rdtag_id'])
                || isset($conditions['join_rbook_id'])
                || isset($conditions['join_rbtag_id'])
            )
            {
                Route::buildRoutePagerConditions($q, $conditions, false, false, 'lo.MainMainAssociation', 'ro');
                
                if (   isset($conditions['join_summit_id'])
                    || isset($conditions['join_summit'])
                    || isset($conditions['join_summit_i18n'])
                    || isset($conditions['join_stag_id'])
                    || isset($conditions['join_sbook_id'])
                    || isset($conditions['join_sbtag_id'])
                )
                {
                    Summit::buildSummitPagerConditions($q, $conditions, false, false, 'lr.MainAssociation', 'sr');
                }
            }
        }

        // join with geo-associations linked to outings
        $conditions = self::joinOnLinkedDocMultiRegions($q, $conditions, array(), false, 'join_oarea', null, 'lo', 'go');
        
        // join with image tables only if needed 
        if (   isset($conditions['join_image_id'])
            || isset($conditions['join_image'])
            || isset($conditions['join_image_i18n'])
            || isset($conditions['join_itag_id']))
        {
            Image::buildImagePagerConditions($q, $conditions, false, 'ui', true);
        }

        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $criteria);
        }
    }

    protected static function buildFieldsList()
    {   
        return array_merge(parent::buildFieldsList(),
                           parent::buildGeoFieldsList(),
                           array('upd.login_name', 'upd.topo_name', 'upd.username', 
                                 'm.lon', 'm.lat', 'm.activities', 'm.category'));
    } 

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }
}
