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
        $q = Doctrine_Query::create()
                           ->select('p.id, p.subject, p.last_post, p.num_replies')
                           ->from('PunbbTopics p');

        if (!empty($langs))
        {
            $q->leftJoin('p.Forum f');
           
            $where = Document::getLanguagesQueryString($langs, 'f');
            $q->addWhere($where, $langs);
        }

        if (!empty($activities) && (empty($langs) || in_array('fr', $langs)))
        {
            if (empty($langs))
            {
                $q->leftJoin('p.Forum f');
            }

            $categories = array(5); // community
            if (in_array(1, $activities)) // skitouring
            {
                $categories[] = 2;
            }
            if (array_intersect(array(2,3,5), $activities)) // snow / mountain / ice climbing
            {
                $categories[] = 3;
            }
            if (in_array(4, $activities)) // rock climbing
            {
                $categories[] = 19;
            }
            if (in_array(6, $activities)) // hiking
            {
                $categories[] = 20;
            }

            $nb_cat = count($categories);
            $cats = array();
            for ($i = 0; $i < $nb_cat; $i++)
            {
                $cats[] = '?';
            }

            $q->addWhere(sprintf('f.cat_id IN (%s)', implode(',', $cats)), $categories);
        }

        $q->addWhere('p.forum_id  IN (2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 18, 20, 21, 22, 23, ' .
                     '24, 25, 41, 46, 47, 50, 51, 70, 72, 58, 59, 60, 61, 62, 63, 64, 65, ' .
                     '67, 68, 69, 80, 81, 83) AND moved_to IS NULL');

        $q->orderBy('p.last_post DESC')->limit($limit);
        return $q->execute(array(), Doctrine::FETCH_ARRAY);
    }
}
