/* anonymize usernames, emails, ip addresses */
/* all users will be renamed to "user<userid>" and passwords set to "c2c" */
-- UPDATE app_users_i18n_archives set name='user' || id;
-- UPDATE app_users_private_data set username='user' || id, password='ea05895d9eed5496a7d8e4786d50b3bb2942d20b', email='user' || id || '@example.com', realname='User ' || id, jabber='', icq='', msn='', aim='', yahoo='', registration_ip='0.0.0.0', password_tmp='', login_name='user' || id, topo_name='User ' || id, search_username='user' || id;
-- UPDATE punbb_posts set poster_ip=NULL, poster_email=NULL;
-- UPDATE punbb_posts_restore set poster_ip=NULL, poster_email=NULL;

/* delete all private messages */
-- DELETE FROM punbb_messages;

/* delete search cache */
-- DELETE FROM punbb_search_cache;
-- DELETE FROM punbb_search_matches;
-- DELETE FROM punbb_search_words;

/* delete mailing-list subscriptions */
-- DELETE FROM subscriber_table;

/* delete all form posts on non-public forums */
-- DELETE from punbb_posts where topic_id in (
--   SELECT id from punbb_topics where forum_id not in (
--     SELECT f.id FROM punbb_forums AS f LEFT JOIN punbb_forum_perms AS fp ON
--       (fp.forum_id=f.id AND fp.group_id=3)
--       WHERE
--       (fp.read_forum IS NULL OR fp.read_forum=1) AND (f.parent_forum_id IS NULL OR f.parent_forum_id=0)));

/* ... then delete all topics from non-public forums */
-- DELETE from punbb_topics where forum_id not in (
--   SELECT f.id FROM punbb_forums AS f LEFT JOIN punbb_forum_perms AS fp ON
--     (fp.forum_id=f.id AND fp.group_id=3)
--     WHERE
--     (fp.read_forum IS NULL OR fp.read_forum=1) AND (f.parent_forum_id IS NULL OR f.parent_forum_id=0));

/* reclaim disk space */
-- VACUUM FULL VERBOSE;
