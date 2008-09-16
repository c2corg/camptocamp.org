-- $Id: db_c2c_user.sql 1949 2007-10-01 23:58:30Z alex $

-- Creation of user C2C (id 2, id 1 is guest user because of PunBB conventions):
BEGIN;
    INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'First user');
    INSERT INTO users_i18n (id, culture, name, description) VALUES(2, 'fr', 'Camptocamp-Association', 'Camptocamp-Association est l\'association qui gère le site.');
    INSERT INTO users (id, is_protected, lon, lat) VALUES (2, FALSE, null, null); --FIXME : lon and lat ?
    INSERT INTO app_users_private_data (id, group_id, username, login_name, topo_name, password, email, document_culture, language) VALUES (2, 1, 'c2c', 'c2c', 'Camptocamp-Association','a94a8fe5ccb19ba61c4c0873d391e987982fbbd3','board@camptocamp.org','fr', 'French');    INSERT INTO app_users_groups (user_id, group_id) VALUES (2, 1); -- superadmin
    INSERT INTO app_users_groups (user_id, group_id) VALUES (2, 3); -- logged
COMMIT; 

