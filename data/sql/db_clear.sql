-- Creates the database structure ...
--
-- $Id: db_clear.sql 338 2007-05-11 07:48:38Z alex $ --
--

DROP TABLE app_cultures CASCADE;
DROP TABLE app_documents_archives CASCADE;
DROP TABLE app_documents_i18n_archives CASCADE;
DROP TABLE app_documents_versions CASCADE;
DROP TABLE app_history_metadata CASCADE;

-- Punbb drop
DROP TABLE punbb_bans CASCADE;
DROP TABLE punbb_categories CASCADE;
DROP TABLE punbb_censoring CASCADE;
DROP TABLE punbb_config CASCADE;
DROP TABLE punbb_forum_perms CASCADE;
DROP TABLE punbb_forums CASCADE;
DROP TABLE punbb_groups CASCADE;
DROP TABLE punbb_online CASCADE;
DROP TABLE punbb_posts CASCADE;
DROP TABLE punbb_ranks CASCADE;
DROP TABLE punbb_reports CASCADE;
DROP TABLE punbb_search_cache CASCADE;
DROP TABLE punbb_search_matches CASCADE;
DROP TABLE punbb_search_words CASCADE;
DROP TABLE punbb_subscriptions CASCADE;
DROP TABLE punbb_topics CASCADE;
DROP TABLE punbb_users CASCADE;

DROP SEQUENCE documents_archives_seq;
DROP SEQUENCE documents_i18n_archives_seq;
DROP SEQUENCE summits_archives_seq;
DROP SEQUENCE summits_i18n_archives_seq;
DROP SEQUENCE users_archives_id_seq;
DROP SEQUENCE users_i18n_archives_id_seq;
DROP SEQUENCE documents_versions_seq;
