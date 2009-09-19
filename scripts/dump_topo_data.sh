#!/bin/bash
# Dump data from guidebook only (no user data, no forum data)

# -T <=> --exclude-table
#pg_dump --data-only -h localhost -p 5432 -T subscriber_table -T punbb_users -T punbb_topics -T punbb_subscriptions \
#-T punbb_search_words -T punbb_search_matches -T punbb_search_cache -T punbb_reports -T punbb_ranks -T punbb_posts \
#-T punbb_polls -T punbb_online -T punbb_messages -T punbb_groups -T punbb_forums -T punbb_forum_perms \
#-T punbb_config -T punbb_censoring -T punbb_categories -T punbb_bans -T app_users_private_data \
#-T app_users_permissions -T app_users_i18n_archives -T app_users_groups -T app_users_archives -T app_remember_keys \
#-T app_groups_permissions -T app_groups -T app_cultures -T app_association_types -T app_messages \
#-T app_permissions

pg_dump --data-only -h localhost -p 5432 -t app_areas_archives -t app_areas_i18n_archives -t app_articles_archives \
-t app_articles_i18n_archives -t app_books_archives -t app_books_i18n_archives -t app_documents_associations \
-t app_documents_versions -t app_geo_associations -t app_history_metadata -t app_huts_archives \
-t app_huts_i18n_archives -t app_images_archives -t app_images_i18n_archives -t app_maps_archives \
-t app_maps_i18n_archives -t app_outings_archives -t app_outings_i18n_archives -t app_parkings_archives \
-t app_parkings_i18n_archives -t app_routes_archives -t app_routes_i18n_archives -t app_sites_archives \
-t app_sites_i18n_archives -t app_summits_archives -t app_summits_i18n_archives c2corg > /tmp/dump_topo_data_c2corg.sql