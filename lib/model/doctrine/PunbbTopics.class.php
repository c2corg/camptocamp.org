<?php
/**
 * $Id: PunbbTopics.class.php 2460 2007-12-03 13:13:59Z alex $
 */

class PunbbTopics extends BasePunbbTopics
{
    /**
     * Returns the list of last active topics.
     * @param integer max number of topics to return
     * @param array list of accepted languages. If empty, accept all languages.
     * @param array list of accepted activities. If empty, accept all activities.
     */
    public static function listLatest($limit, $langs, $activities)
    {
        if (empty($langs) && empty($activities))
        {
            $forums = self::getAllForumsIds();
        }
        else
        {
            if (!empty($langs))
            {
                $forums_by_lang = array();
                foreach ($langs as $lang)
                {
                    switch ($lang)
                    {
                        case 'fr': array_push($forums_by_lang, 4, 11, 24, 25, 2, 7, 20, 5, 8, 21, 10, 22, 79, 6, 9, 23); break;
                        case 'it': array_push($forums_by_lang, 41, 50, 51, 70, 72); break;
                        case 'en': array_push($forums_by_lang, 58, 59, 60); break;
                        case 'de': array_push($forums_by_lang, 61, 62, 63); break;
                        case 'es': array_push($forums_by_lang, 64, 65, 66); break;
                        case 'ca': array_push($forums_by_lang, 67, 68, 69); break;
                        case 'eu': array_push($forums_by_lang, 80, 81, 83);
                    }
                }
            }
            else
            {
                // no filter by lang => list all "public" forums
                $forums_by_lang = self::getAllForumsIds();
            }
    
            if (!empty($activities))
            {
                // misc forums (community, etc.)
                $forums_by_act = array(4, 9, 11, 18, 24, 25, 41, 50, 51, 70, 72);
                foreach ($activities as $activity)
                {
                    switch ($activity)
                    {
                        case 1: array_push($forums_by_act, 2, 7, 20, 58, 61, 64, 67, 80); break; // skitouring
                        case 2: case 3: case 5: array_push($forums_by_act, 5, 8, 21, 60, 63, 66, 69, 81); break; // snow / mountain / ice climbing
                        case 4: array_push($forums_by_act, 10, 22, 79, 59, 62, 65, 68, 83); break; // rock climbing
                        case 6: array_push($forums_by_act, 6, 23, 60, 63, 66, 69, 81); // hiking
                    }
                }
                $forums_by_act = array_unique($forums_by_act);
            }
            else
            {
                $forums_by_act = self::getAllForumsIds();
            }

            $forums = array_intersect($forums_by_lang, $forums_by_act);
        }

        return self::listLatestById($limit, $forums);
    }

    /**
     * Returns list of last active topics regarding mountain news
     */
    public static function listLatestMountainNews($limit, $langs)
    {
        if (empty($langs))
        {
            $forums = array(18, 84, 85, 86, 87, 48, 89, 90, 91, 92, 93);
        }
        else
        {
            $forums = array();
            foreach ($langs as $lang)
            {
                switch($lang)
                {
                    case 'fr': array_push($forums, 18, 84, 85, 86, 87); break;
                    case 'it': array_push($forums, 48); break;
                    case 'en': array_push($forums, 89); break;
                    case 'de': array_push($forums, 90); break;
                    case 'es': array_push($forums, 91); break;
                    case 'ca': array_push($forums, 92); break;
                    case 'eu': array_push($forums, 93);
                }
            }
        }
        return self::listLatestById($limit, $forums);
    }

    protected static function listLatestById($limit, $f_ids)
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
               ->select('p.id, p.subject, p.last_post, p.num_replies')
               ->from('PunbbTopics p')
               ->addWhere('p.moved_to IS NULL')
               ->addWhere(sprintf('p.forum_id IN (%s)', implode(',', $f)), $f_ids)
               ->orderBy('p.last_post DESC')
               ->limit($limit);

        return $q->execute(array(), Doctrine::FETCH_ARRAY);
    }

    protected static function getAllForumsIds() // no news topic
    {
        return array(4, 11, 24, 25, 2, 7, 20, 5, 8, 21, 10, 22, 79, 6, 9, 23,
                     41, 50, 51, 70, 72,
                     58, 59, 60,
                     61, 62, 63,
                     64, 65, 66,
                     67, 68, 69,
                     80, 81, 83);
    }
}
