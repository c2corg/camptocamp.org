<?php
/**
 * Model for groups
 * $Id: Group.class.php 1019 2007-07-23 18:36:35Z alex $
 */
class Group extends BaseGroup
{
	/**
     * Return a Doctrine_Collection that match the array of names
     *
     * @param array names
     * @return array Doctrine_Collection
	 */
    public static function findGroupIdByNames($names)
    {
    	for ($i = 0, $n = count($names); $i < $n; $i++)
        {
        	if ($i == 0)
            {
            	$where = ' Group.name = ?';
            }
            else
            {
                $where .= ' OR Group.name = ?';
            }
        }

    	return Doctrine_Query::create()->
    	                       select('Group.id')->
    	                       from('Group')->
    	                       where($where, $names)->
    	                       execute();
    }
}
