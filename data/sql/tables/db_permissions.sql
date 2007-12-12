-- Creates the permissions tables ...
--
-- $Id: db_permissions.sql 1134 2007-08-01 14:57:35Z fvanderbiest $ --
--

-- The permissions table
CREATE SEQUENCE permissions_id_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_permissions (
		id integer NOT NULL DEFAULT nextval('permissions_id_seq'::text),
		name character varying(50) NOT NULL UNIQUE
);
ALTER TABLE app_permissions ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);

-- Add default permissions
INSERT INTO app_permissions (name) VALUES('admin');                     -- id = 1
INSERT INTO app_permissions (name) VALUES('contributor_management');    -- id = 2
INSERT INTO app_permissions (name) VALUES('member_management');         -- id = 3
-- start with a basic moderator right
INSERT INTO app_permissions (name) VALUES('moderator');                 -- id = 4
-- INSERT INTO app_permissions (name) VALUES('guidebook_moderator');       -- id = 5
-- INSERT INTO app_permissions (name) VALUES('articles_moderator');        -- id = 6
-- INSERT INTO app_permissions (name) VALUES('links_moderator');           -- id = 7


-- The users_permissions table
CREATE TABLE app_users_permissions (
		user_id integer NOT NULL,
		permission_id integer NOT NULL 
);
ALTER TABLE app_users_permissions ADD CONSTRAINT users_permissions_pkey PRIMARY KEY (user_id, permission_id);
ALTER TABLE app_users_permissions ADD CONSTRAINT fk_permissions FOREIGN KEY (permission_id) REFERENCES app_permissions(id);
CREATE INDEX app_users_permissions__users_idx ON app_users_permissions USING btree (user_id);
--CREATE INDEX app_users_permissions__groups_idx ON app_users_permissions USING btree (permission_id); -- uncomment if we have to search for all users matching a specific permission -- (dubious).


-- The groups_permissions table
CREATE TABLE app_groups_permissions (
		group_id integer NOT NULL,
		permission_id integer NOT NULL
);
ALTER TABLE app_groups_permissions ADD CONSTRAINT groups_permissions_pkey PRIMARY KEY (group_id, permission_id);
ALTER TABLE app_groups_permissions ADD CONSTRAINT fk_group FOREIGN KEY (group_id) REFERENCES app_groups(id);
ALTER TABLE app_groups_permissions ADD CONSTRAINT fk_permission FOREIGN KEY (permission_id) REFERENCES app_permissions(id);
-- no specific index here because small table => direct scan costs less.
-- needs confirmation

-- Add default group permissions
INSERT INTO app_groups_permissions (group_id, permission_id) VALUES(1,1); -- superadmin -> all rights
INSERT INTO app_groups_permissions (group_id, permission_id) VALUES(1,2); -- superadmin
INSERT INTO app_groups_permissions (group_id, permission_id) VALUES(1,3); -- superadmin
INSERT INTO app_groups_permissions (group_id, permission_id) VALUES(1,4); -- superadmin

INSERT INTO app_groups_permissions (group_id, permission_id) VALUES(2,2); -- admin -> all rights, except admin
INSERT INTO app_groups_permissions (group_id, permission_id) VALUES(2,3); -- admin
INSERT INTO app_groups_permissions (group_id, permission_id) VALUES(2,4); -- admin


-- two read-only VIEWS that puts in one place the permissions granted to each user, individually (via app_users_permissions) AND to the group he belongs to (via app_groups_permissions):
CREATE OR REPLACE VIEW user_own_permissions AS
SELECT u.id AS user_id, p.id AS permission_id 
FROM users u
LEFT JOIN (app_users_permissions up LEFT JOIN app_permissions p ON (up.permission_id = p.id)) ON u.id = up.user_id 
GROUP BY u.id, p.id;


CREATE OR REPLACE VIEW user_group_permissions AS
SELECT u.id AS user_id, p.id AS permission_id 
FROM users u
LEFT JOIN (app_users_groups ug LEFT JOIN (app_groups g LEFT JOIN (app_groups_permissions gp LEFT JOIN app_permissions p ON (gp.permission_id = p.id)) ON (gp.group_id = p.id)) ON (ug.group_id = g.id)) ON u.id = ug.user_id 
GROUP BY u.id, p.id;
