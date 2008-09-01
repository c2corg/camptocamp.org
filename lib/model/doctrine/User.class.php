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

    // TODO: make it generic for any kind of document (pass type as argument?)
    public static function findSummitsForUserId($id)
    {
        return Doctrine_Query::create()
                             ->from('User.summits')
                             ->where('User.id = ?', array($id))
                             ->limit(1)
                             ->execute()
                             ->getFirst()
                             ->get('summits');
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

    public static function browse($sort, $criteria)
    {   
        $pager = self::createPager('User', self::buildFieldsList(), $sort);
        $q = $pager->getQuery();
    
        self::joinOnRegions($q);
        $q->leftJoin('m.private_data pd');

        if (!empty($criteria))
        {
            // some criteria have been defined => filter list on these criteria.
            // In that case, personalization is not taken into account.
            $q->addWhere(implode(' AND ', $criteria[0]), $criteria[1]);
        }
        elseif (c2cPersonalization::isMainFilterSwitchOn())
        {
            self::filterOnRegions($q);
        }
        else
        {
            $pager->simplifyCounter();
        }

        return $pager;
    }   

    protected static function buildFieldsList()
    {   
        return array_merge(parent::buildFieldsList(), 
                           parent::buildGeoFieldsList(),
                           array('pd.login_name', 'pd.topo_name',
                                 'pd.username'));
    } 
}
