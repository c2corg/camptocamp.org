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

    public static function buildUserListCriteria(&$criteria, &$params_list, $is_module = false, $mid = 'main_id')
    {
        if (empty($params_list))
        {
            return null;
        }
        
        $conditions = $values = $joins = array();
        
        if ($is_module)
        {
            $m = 'm';
            $m2 = 'u';
            $mid = 'm.id';
            $midi18n = $mid;
            $join = null;
            $join_id = null;
            $join_idi18n = null;
            $join_i18n = 'user_i18n';
            $join_private_data = 'user_pd';
        }
        else
        {
            $m = 'u';
            $m2 = $m;
            $mid = array('l' . $m, $mid);
            $midi18n = implode('.', $mid);
            $join = 'user';
            $join_id = $join . '_id';
            $join_idi18n = $join . '_idi18n';
            $join_i18n = $join . '_i18n';
            $join_private_data = $join . '_pd';
        }
        
        $nb_id = 0;
        $nb_name = 0;
        
        if ($is_module)
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, array('id', 'users'), $join_id);
        }
        else
        {
            $nb_id = self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'users', $join_id);
        }
        $has_id = ($nb_id == 1);
        
        if (!$has_id)
        {
            if ($is_module)
            {
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'u', 'activities'), 'act', $join);
                self::buildConditionItem($conditions, $values, $joins, $params_list, 'Georef', $join, 'geom', $join);
                
                $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'Mstring', array(array($midi18n, 'ui.search_name'), array($midi18n, 'upd.search_username')), 'utfnam', array(array($join_idi18n, $join_i18n), array($join_idi18n, $join_private_data)), array('User', 'UserPrivateData'));
                if ($nb_name === 'no_result')
                {
                    return $nb_name;
                }
                elseif ($nb_name[0]['nb_result'] == 0)
                {
                    $nb_id += $nb_name[1]['nb_result'];
                }
                elseif ($nb_name[1]['nb_result'] == 0)
                {
                    $nb_id += $nb_name[0]['nb_result'];
                }
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
                }
                if (count($friend_ids))
                {
                    $params_list['friends'] = implode('-', $friend_ids);
                    if ($is_module)
                    {
                        self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $mid, 'friends', $join_id);
                    }
                    else
                    {
                        self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', $mid, 'friends', $join_id);
                    }
                }
                else
                {
                    return 'no_result';
                }
            }
            
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Around', $m2 . '.geom', 'uarnd', $join);
            
            $nb_name = self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'ui.search_name'), ($is_module ? array('unam', 'name') : 'unam'), array($join_idi18n, $join_i18n), 'User');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'String', array($midi18n, 'upd.search_username'), 'ufnam', array($join_idi18n, $join_private_data), 'UserPrivateData');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            $nb_id += $nb_name;
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'Array', array($m, 'u', 'activities'), 'uact', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', $m . '.category', 'ucat', $join);
            self::buildConditionItem($conditions, $values, $joins, $params_list, 'List', 'ui.culture', 'ucult', $join_i18n);
            
            // article criteria
            $nb_name = Article::buildArticleListCriteria($criteria, $params_list, false, 'u', 'linked_id');
            if ($nb_name === 'no_result')
            {
                return $nb_name;
            }
            
            if (isset($criteria[2]['join_uarticle']))
            {
                $joins['join_user'] = true;
                if (!$is_module)
                {
                    $joins['post_user'] = true;
                }
            }
        }
        
        if (!empty($conditions))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
        }
        if (!empty($joins))
        {
            $joins['join_user'] = true;
        }
        if ($is_module && $nb_id)
        {
            $joins['nb_id'] = $nb_id;
        }
        $criteria[2] += $joins;
        
        return null;
    }
    
    public static function buildListCriteria($params_list)
    {
        $criteria = $conditions = $values = $joins = $joins_order = array();
        $criteria[0] = array(); // conditions
        $criteria[1] = array(); // values
        $criteria[2] = array(); // joins
        $criteria[3] = array(); // joins for order

        // criteria for disabling personal filter
        self::buildPersoCriteria($conditions, $values, $joins, $params_list, 'users');

        // criteria to hide users whithout public profile
        if (!sfContext::getInstance()->getUser()->isConnected())
        {
            $conditions[] = 'upd.is_profile_public IS TRUE';
            $joins['user_pd'] = true;
            $joins['join_user'] = true;
            if (isset($joins['all']))
            {
                unset($joins['all']);
            }
        }
        
        // orderby criteria
        $orderby_list = c2cTools::getRequestParameterArray(array('orderby', 'orderby2', 'orderby3'));
        
        self::buildOrderCondition($joins_order, $orderby_list, array('unam'), array('user_i18n', 'join_user'));
        self::buildOrderCondition($joins_order, $orderby_list, array('ufnam'), array('user_pd', 'join_user'));
        
        // area criteria
        self::buildAreaCriteria($criteria, $params_list, 'u');
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'MultiId', array('go', 'linked_id'), 'oareas', 'oarea');
        if (isset($joins['oarea']))
        {
            $joins['join_outing'] = true;
            $joins['post_outing'] = true;
        }
        
        // return if no criteria
        if (isset($joins['all']) || empty($params_list))
        {
            $criteria[0] = array_merge($criteria[0], $conditions);
            $criteria[1] = array_merge($criteria[1], $values);
            $criteria[2] += $joins;
            $criteria[3] += $joins_order;
            return $criteria;
        }
        
        // user criteria
        $has_name = User::buildUserListCriteria($criteria, $params_list, true);
        self::buildConditionItem($conditions, $values, $joins, $params_list, 'Join', '', 'ijoin', '', 'image');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
       
        // outing criteria
        $has_name = Outing::buildOutingListCriteria($criteria, $params_list, false, 'linked_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // route criteria
        $has_name = Route::buildRouteListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        // summit criteria
        $has_name = Summit::buildSummitListCriteria($criteria, $params_list, false, 'main_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }
        
        // image criteria
        $has_name = Image::buildImageListCriteria($criteria, $params_list, false, 'document_id');
        if ($has_name === 'no_result')
        {
            return $has_name;
        }

        $criteria[0] = array_merge($criteria[0], $conditions);
        $criteria[1] = array_merge($criteria[1], $values);
        $criteria[2] += $joins;
        $criteria[3] += $joins_order;
        return $criteria;
    }

    public static function buildMainPagerConditions(&$q, $criteria)
    {
        self::joinOnRegions($q);
        $q->leftJoin('m.private_data upd');
    }
    
    public static function buildUserPagerConditions(&$q, &$joins, $is_module = false, $is_linked = false, $first_join = null, $ltype = null)
    {
        $join = 'user';
        if ($is_module)
        {
            $m = 'm';
            $linked = '';
            $main_join = $m . '.associations';
            $linked_join = $m . '.LinkedAssociation';
            $private_data = 'private_data';
        }
        else
        {
            $m = 'lu';
            $private_data = 'UserPrivateData';
            if ($is_linked)
            {
                $linked = 'Linked';
                $main_join = $m . '.MainMainAssociation';
                $linked_join = $m . '.LinkedAssociation';
            }
            else
            {
                $linked = '';
                $main_join = $m . '.MainAssociation';
                $linked_join = $m . '.LinkedLinkedAssociation';
            }
            $join_id = $join . '_id';
                
            if (isset($joins[$join_id]))
            {
                self::joinOnMulti($q, $joins, $join_id, $first_join . " $m", 5);
                
                if (isset($joins[$join_id . '_has']))
                {
                    $q->addWhere($m . "1.type = '$ltype'");
                }
            }
            
            if (   isset($joins['post_' . $join])
                || isset($joins[$join])
                || isset($joins[$join . '_idi18n'])
                || isset($joins[$join . '_i18n'])
            )
            {
                $q->leftJoin($first_join . " $m");
                
                if (   isset($joins['post_' . $join])
                    || isset($joins[$join])
                    || isset($joins[$join . '_i18n'])
                )
                {
                    if ($ltype)
                    {
                        $q->addWhere($m . ".type = '$ltype'");
                    }
                }
                
                if (isset($joins[$join]))
                {
                    $q->leftJoin($m . '.' . $linked . 'User u');
                }
            }
        }

        if (isset($joins[$join . '_pd']))
        {
            $q->leftJoin($m . '.' . $linked . $private_data . ' upd');
        }

        if (isset($joins[$join . '_i18n']))
        {
            $q->leftJoin($m . '.' . $linked . 'UserI18n ui');
        }
        
        if (isset($joins['join_uarticle']))
        {
            Article::buildArticlePagerConditions($q, $joins, false, 'u', false, $linked_join, 'uc');
        }
    }
    
    public static function buildPagerConditions(&$q, $criteria)
    {
        $conditions = $criteria[0];
        $values = $criteria[1];
        $joins = $criteria[2];
        
        self::buildAreaIdPagerConditions($q, $joins);
        
        // join with users tables only if needed 
        if (isset($joins['join_user']))
        {
            User::buildUserPagerConditions($q, $joins, true);
        }

        // join with image tables only if needed 
        if (isset($joins['join_image']))
        {
            Image::buildImagePagerConditions($q, $joins, false, 'ui', true);
        }

        // join with outings tables only if needed 
        if (   isset($joins['join_route'])
            || isset($joins['join_summit'])
        )
        {
            $joins['join_outing'] = true;
            $joins['post_outing'] = true;
        }
        
        if (isset($joins['join_outing']))
        {
            Outing::buildOutingPagerConditions($q, $joins, false, true, 'm.LinkedAssociation', 'uo');
            
            if (isset($joins['join_summit']))
            {
                $joins['join_route'] = true;
                $joins['post_route'] = true;
            }
            
            if (isset($joins['join_route']))
            {
                Route::buildRoutePagerConditions($q, $joins, false, false, 'lo.MainMainAssociation', 'ro');
                
                if (isset($joins['join_summit']))
                {
                    Summit::buildSummitPagerConditions($q, $joins, false, false, 'lr.MainAssociation', 'sr');
                }
            }

            // join with geo-associations linked to outings
            self::joinOnLinkedDocMultiRegions($q, $joins, array(), false, 'oarea', null, 'lo', 'go');
        }

        if (!empty($conditions))
        {
            $q->addWhere(implode(' AND ', $conditions), $values);
        }
    }

    public static function getSortField($orderby, $mi = 'mi')
    {   
        switch ($orderby)
        {
            case 'unam': return $mi . '.search_name';
            case 'ufnam': return 'upd.search_username';
            case 'range': return 'gr.linked_id';
            case 'admin': return 'gd.linked_id';
            case 'country': return 'gc.linked_id';
            case 'valley': return 'gv.linked_id';
            case 'act':  return 'm.activities';
            case 'ucat':  return 'm.category';
            case 'lat': return 'm.lat';
            case 'lon': return 'm.lon';
            default: return NULL;
        }
    }

    protected static function buildFieldsList($main_query = false, $mi = 'mi', $format = null, $sort = null, $custom_fields = null)
    {   
        if ($main_query)
        {
            $data_fields_list = array('upd.login_name', 'upd.topo_name', 'upd.username', 
                                 'm.lon', 'm.lat', 'm.activities', 'm.category');
            $data_fields_list = array_merge($data_fields_list,
                                            parent::buildGeoFieldsList());
            if (in_array('mail', $custom_fields) && sfContext::getInstance()->getUser()->hasCredential('moderator'))
            {
                $data_fields_list[] = 'upd.email';
            }
        }
        else
        {
            $data_fields_list = array();
        }
        
        $base_fields_list = parent::buildFieldsList($main_query, $mi, $format, $sort, $custom_fields);
        
        return array_merge($base_fields_list, 
                           $data_fields_list);
    } 

    protected function addPrevNextIdFilters($q, $model)
    {
        self::joinOnRegions($q);
        self::filterOnRegions($q);
    }
}
