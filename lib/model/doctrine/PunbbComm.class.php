<?php
/**
 * $Id: PunbbComm.class.php 1019 2007-07-23 18:36:35Z alex $
 */

class PunbbComm extends BasePunbbComm
{
    public static function GetComments($topic_subject)
    {
        return Doctrine_Query::create()
                             ->select('p.id, p.poster, p.poster_id, p.poster_email, p.message, p.posted, p.topic_id, t.id, t.poster, t.subject, t.num_replies,t.forum_id')
                             ->from('PunbbComm p, p.Topic t')
                             ->where('t.forum_id = 1 AND t.id = p.topic_id AND t.subject = ?', array($topic_subject))
		                         ->orderby('p.id')
                             ->execute();
    }
    
    public static function GetNbComments($topic_subject)
    {
        return Doctrine_Query::create()
                             ->select('COUNT(p.id) num_comments')
                             ->from('PunbbComm p, p.Topic t')
                             ->where('t.forum_id = 1 AND t.id = p.topic_id AND t.subject = ?', array($topic_subject))
                             ->execute()->getFirst()->num_comments;
    }
    
}
