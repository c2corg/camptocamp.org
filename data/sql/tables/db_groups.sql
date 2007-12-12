-- Creates the groups tables ...
--
-- $Id: db_groups.sql 1134 2007-08-01 14:57:35Z fvanderbiest $ --
--

-- The group table
CREATE SEQUENCE groups_id_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_groups (
		id integer NOT NULL DEFAULT nextval('groups_id_seq'::text),
		name character varying(50) NOT NULL UNIQUE
);
ALTER TABLE app_groups ADD CONSTRAINT groups_pkey PRIMARY KEY (id);

-- Add default groups
INSERT INTO app_groups (name) VALUES('superadmin');         -- id = 1
INSERT INTO app_groups (name) VALUES('admin');              -- id = 2
INSERT INTO app_groups (name) VALUES('logged');             -- id = 3
INSERT INTO app_groups (name) VALUES('pending');            -- id = 4
INSERT INTO app_groups (name) VALUES('inactive');           -- id = 5
-- moderator groups
-- INSERT INTO app_groups (name) VALUES('supermoderator');     -- id = 6 --> all moderator rights
-- INSERT INTO app_groups (name) VALUES('guide');
-- INSERT INTO app_groups (name) VALUES('accompagnateur');
-- INSERT INTO app_groups (name) VALUES('moniteur');
-- INSERT INTO app_groups (name) VALUES('pisteur');
-- INSERT INTO app_groups (name) VALUES('meteorologue');
-- INSERT INTO app_groups (name) VALUES('traducteur');
-- INSERT INTO app_groups (name) VALUES('developpeur');


-- The users_groups table
CREATE TABLE app_users_groups (
		user_id integer NOT NULL,
		group_id integer NOT NULL
);
ALTER TABLE app_users_groups ADD CONSTRAINT users_groups_pkey PRIMARY KEY (user_id, group_id); -- unicity is thus garanteed
ALTER TABLE app_users_groups ADD CONSTRAINT fk_group FOREIGN KEY (group_id) REFERENCES app_groups(id);
CREATE INDEX app_users_groups__users_idx ON app_users_groups USING btree (user_id);
--CREATE INDEX app_users_groups__groups_idx ON app_users_groups USING btree (group_id); -- uncomment if we have to search for all users matching a group.