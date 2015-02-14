<?php
/**
 * $Id: PunbbTopics.class.php 2460 2007-12-03 13:13:59Z alex $
 */

class PunbbTopics extends BasePunbbTopics
{
    /**
     * Returns the list of last active topics (only 'public' ones, that will appear on homepage)
     * @param integer max number of topics to return
     * @param array list of accepted languages. If empty, accept all languages.
     * @param array list of accepted activities. If empty, accept all activities.
     */
    public static function listLatest($limit, $langs, $activities, $forums = null)
    {
        $forums = self::getForumIds('app_forum_public_ids', $langs, $activities, $forums);
        return self::listLatestById($limit, $forums);
    }

    /**
     * Returns list of last active topics regarding mountain news
     * @param integer max number of topics to return
     * @param array list of accepted languages. If empty, accept all languages.
     * @param array list of accepted activities. If empty, accept all activities.
     */
    public static function listLatestMountainNews($limit, $langs, $activities, $forums = null)
    {
        $forums = self::getForumIds('app_forum_mountain_news', $langs, $activities, $forums);
        return self::listLatestById($limit, $forums);
    }

    public static function listLatestC2cNews($limit, $langs)
    {
        $forums = self::getForumIds('app_forum_c2c_news', $langs, null);
        return self::listLatestById($limit, $forums);
    }

    public static function getC2cNewsForumId($lang)
    {
        $forums = self::getForumIds('app_forum_c2c_news', array($lang), null);
        return (count($forums) > 0) ? $forums[0] : 0;
    }

    public static function getForumIds($conf_prefix, $langs, $activities, $forums = null)
    {
        if (empty($forums))
        {
            if (empty($langs) && empty($activities))
            {
                return sfConfig::get($conf_prefix);
            }

            /* lang filter */
            if (!empty($langs))
            {
                $a = sfConfig::get($conf_prefix.'_by_lang');
                $forums_by_lang = array();
                foreach ($langs as $lang)
                {
                    if (isset($a[$lang]))
                    {
                        $forums_by_lang = array_merge($forums_by_lang, $a[$lang]);
                    }
                }
            }
            else
            {
                $forums_by_lang = sfConfig::get($conf_prefix);
            }

            /* activity filter */
            if (!empty($activities))
            {
                $a = sfConfig::get($conf_prefix.'_by_activity');
                $forums_by_act = $a['misc'];
                foreach ($activities as $activity)
                {
                    $forums_by_act = array_merge($forums_by_act, $a[$activity]);
                }
                $forums_by_act = array_unique($forums_by_act);
            }
            else
            {
                $forums_by_act = sfConfig::get($conf_prefix);
            }

            /* lang & activity intersection */
            return array_intersect($forums_by_lang, $forums_by_act);
        }
        else
        {
            /* lang filter */
            $a = sfConfig::get($conf_prefix.'_by_lang');
            $forums_ids = array();
            if (!empty($langs))
            {
                foreach ($langs as $lang)
                {
                    if (isset($forums[$lang]))
                    {
                        $forums_ids_lang = $forums[$lang];
                    }
                    else
                    {
                        $forums_ids_lang = $a[$lang];
                    }
                    $forums_ids = array_merge($forums_ids, $forums_ids_lang);
                }
            }
            else
            {
                foreach ($a as $lang => $default_forums)
                {
                    if (isset($forums[$lang]))
                    {
                        $forums_ids_lang = $forums[$lang];
                    }
                    else
                    {
                        $forums_ids_lang = $default_forums;
                    }
                    $forums_ids = array_merge($forums_ids, $forums_ids_lang);
                }
            }
            return $forums_ids;
        }
    }

    public static function listLatestById($limit, $f_ids)
    {
        if (count($f_ids) > 0)
        {
            $f = array();
            for ($i = 0; $i < count($f_ids); $i++) $f[] = '?';
        }
        else
        {
            return null;
        }

        if (!is_integer($limit) || $limit <= 0)
        {
            return null;
        }

        $q = Doctrine_Query::create()
               ->select('p.id, p.subject, p.last_post, p.num_replies, p.forum_id')
               ->from('PunbbTopics p')
               ->addWhere('p.moved_to IS NULL')
               ->addWhere(sprintf('p.forum_id IN (%s)', implode(',', $f)), $f_ids)
               ->orderBy('p.last_post DESC')
               ->limit($limit);

        return $q->execute(array(), Doctrine::FETCH_ARRAY);
    }
}
