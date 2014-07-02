<?php
/**
 * $Id: PunbbComm.class.php 1019 2007-07-23 18:36:35Z alex $
 */

class PunbbComm extends BasePunbbComm
{
    // Retrieve comments for a document_lang
    // All doc comments topics are in forum 1
    public static function GetComments($topic_subject)
    {
        return Doctrine_Query::create()
                             ->select('p.id, p.poster, p.poster_id, p.poster_email, p.message, p.posted, p.topic_id, t.id, t.poster, t.subject, t.num_replies, t.forum_id')
                             ->from('PunbbComm p, p.Topic t')
                             ->where('t.forum_id = 1 AND t.id = p.topic_id AND t.subject = ?', array($topic_subject))
		                         ->orderby('p.id')
                             ->execute();
    }

    // Retrieve comments count for a document_lang
    // All doc comments topics are in forum 1
    // If $topic is a string, we return a number
    // If $topic is an array, we return an array with topic + count
    public static function GetNbComments($topic_subject)
    {
        if (is_string($topic_subject))
        {
            return Doctrine_Query::create()
                                 ->select('COUNT(p.id) nb_comments')
                                 ->from('PunbbComm p, p.Topic t')
                                 ->where('t.forum_id = 1 AND t.id = p.topic_id AND t.subject = ?', array($topic_subject))
                                 ->execute()->getFirst()->nb_comments;
        }
        else if (is_array($topic_subject))
        {
            $sql = 'SELECT COUNT(p.id) nb_comments, p2.subject FROM punbb_posts p LEFT JOIN punbb_topics p2 ON p.topic_id = p2.id ' .
                   'WHERE (p2.forum_id = 1 AND p2.id = p.topic_id AND p2.subject IN ( ' ."'". implode($subjects, "', '") ."'". ')) GROUP BY p2.subject';

            return sfDoctrine::connection()->standaloneQuery($sql)->fetchAll();
        }
        else
        {
            return null;
        }
    }
    
}
