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
     */
    public static function listLatest($limit, $langs)
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

        $q->addWhere('p.forum_id  IN (2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 18, 20, 21, 22, 23, ' .
                     '24, 25, 41, 42, 43, 44, 48, 49, 50, 72, 58, 59, 60, 61, 62, 63, 64, 65, ' .
                     '67, 68, 69) AND moved_to IS NULL');

        return $q->orderBy('p.last_post DESC')
                 ->limit($limit)
                 ->execute(array(), Doctrine::FETCH_ARRAY);
    }
}
