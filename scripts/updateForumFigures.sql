--
-- Computes forums and topics stats (nb of posts, topics, last posters, etc.)
-- $Id: updateForumFigures.sql 1793 2007-09-24 23:12:35Z alex $
--

UPDATE punbb_topics SET
  last_post = (SELECT MAX(posted) FROM punbb_posts WHERE topic_id = punbb_topics.id),
  last_post_id = (SELECT MAX(id) FROM punbb_posts WHERE topic_id = punbb_topics.id),
  last_poster = (SELECT poster FROM punbb_posts WHERE id = (SELECT MAX(id) FROM punbb_posts WHERE topic_id = punbb_topics.id)),
  num_replies = (SELECT COUNT(*) - 1 FROM punbb_posts WHERE topic_id = punbb_topics.id);

UPDATE punbb_forums SET
  num_topics = (SELECT COUNT(*) FROM punbb_topics WHERE forum_id = punbb_forums.id),
  num_posts = (SELECT SUM(num_replies + 1) FROM punbb_topics WHERE forum_id = punbb_forums.id),
  last_post = (SELECT MAX(last_post) FROM punbb_topics WHERE forum_id = punbb_forums.id),
  last_post_id = (SELECT MAX(last_post_id) FROM punbb_topics WHERE forum_id = punbb_forums.id),
  last_poster = (SELECT last_poster FROM punbb_topics WHERE last_post_id = (SELECT MAX(last_post_id) FROM punbb_topics WHERE forum_id = punbb_forums.id))
  WHERE id IN (SELECT DISTINCT forum_id FROM punbb_topics);
