-- Creates the database structure ...
--
-- $Id: db_general.sql 2247 2007-11-02 13:56:21Z alex $ --
--

-- Cultures table : as many records as we wish to manage languages
-- 
CREATE TABLE app_cultures (
    culture char(2) NOT NULL
);
ALTER TABLE app_cultures ADD CONSTRAINT cultures_pkey PRIMARY KEY (culture); 
COMMENT ON TABLE app_cultures IS 'Cultures table : as many records as we wish to manage languages';

INSERT INTO app_cultures (culture) VALUES ('ca');
INSERT INTO app_cultures (culture) VALUES ('de');
INSERT INTO app_cultures (culture) VALUES ('en');
INSERT INTO app_cultures (culture) VALUES ('es');
INSERT INTO app_cultures (culture) VALUES ('fr');
INSERT INTO app_cultures (culture) VALUES ('it');

-- Table app_documents_archives 
CREATE SEQUENCE documents_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_documents_archives (
    document_archive_id integer NOT NULL DEFAULT nextval('documents_archives_seq'::text),
    id integer NOT NULL DEFAULT nextval('documents_id_seq'::text),
    module character varying(20) NOT NULL,
    lon numeric(9,6), -- centroid  
    lat numeric(9,6), -- centroid
    elevation smallint, -- of centroid : should be in daughter tables because some of them (users, areas) are pure 2D 
    -- but on the other hand, keeping it here avoids a join for tooltips ... 
    -- so we keep it here, and update accordingly the update_geom procedures to take this into account !
    is_protected boolean NOT NULL DEFAULT FALSE, -- is this document protected in edition ?
    redirects_to integer, -- has this document been fusioned with another one, and which one is it ?
    geom_wkt text,  -- the geometry expressed in well-known-text
    is_latest_version boolean
)  WITH OIDS;
ALTER TABLE ONLY app_documents_archives ADD CONSTRAINT documents_archives_pkey PRIMARY KEY (document_archive_id);
CREATE INDEX app_documents_archives_id_idx ON app_documents_archives USING btree (id);
CREATE INDEX app_documents_archives_module_idx ON app_documents_archives USING btree (module); -- useful for "whatsnew" requests in a module
CREATE INDEX app_documents_archives_redirects_idx ON app_documents_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_documents_archives_latest_idx ON app_documents_archives USING btree (is_latest_version); 

SELECT AddGeometryColumn('app_documents_archives', 'geom', 900913, 'GEOMETRY', 3); 
ALTER TABLE app_documents_archives DROP CONSTRAINT enforce_dims_geom; -- we drop the constraint on the parent table to build it on this same field in the daughter tables.
CREATE INDEX app_documents_archives_geom_idx ON app_documents_archives USING GIST (geom GIST_GEOMETRY_OPS); 

--CREATE INDEX app_documents_archives_geom_idx ON app_documents_archives USING GIST (geom_wkb GIST_GEOMETRY_OPS); 

-- Table app_documents_i18n_archives
CREATE SEQUENCE documents_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;
CREATE TABLE app_documents_i18n_archives (
    document_i18n_archive_id integer NOT NULL DEFAULT nextval('documents_i18n_archives_seq'::text),
    id integer NOT NULL DEFAULT 0,
    culture char(2) NOT NULL,
    name character varying(150) NOT NULL,
    search_name character varying(150),
    description text,
    is_latest_version boolean
);
ALTER TABLE ONLY app_documents_i18n_archives ADD CONSTRAINT documents_i18n_archives_pkey PRIMARY KEY (document_i18n_archive_id);
ALTER TABLE app_documents_i18n_archives ADD CONSTRAINT fk_documents_i18n_archives_culture FOREIGN KEY (culture) REFERENCES app_cultures(culture);
-- this multicolumn index on (id, culture) is very efficient for requests on both fields, but also efficient when only 'id' field is requested
-- see for instance http://docs.postgresqlfr.org/8.2/indexes-multicolumn.html
CREATE INDEX app_documents_i18n_archives_id_culture_idx ON app_documents_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_documents_i18n_archives_name_idx ON app_documents_i18n_archives USING btree (search_name); 
CREATE INDEX app_documents_i18n_archives_latest_idx ON app_documents_i18n_archives USING btree (is_latest_version); 

-- history metadata management (user_id, comment, is_minor)

CREATE SEQUENCE history_metadata_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;
CREATE TABLE app_history_metadata (
    history_metadata_id integer NOT NULL DEFAULT nextval('history_metadata_seq'::text),
    user_id integer NOT NULL,   -- the user who made the last revision of the document
    is_minor boolean NOT NULL DEFAULT FALSE, -- flag specifying whether the revision is minor (TRUE) or not.
    comment character varying(200), -- comment for the document's last revision 
    written_at timestamp without time zone NOT NULL DEFAULT NOW()
);
ALTER TABLE app_history_metadata ADD CONSTRAINT history_metadata_pkey PRIMARY KEY (history_metadata_id);
-- index useful for user's latest editions:
CREATE INDEX app_history_metadata_user_idx ON app_history_metadata USING btree (user_id);


-- Table app_documents_versions (the one that keeps track of history for every document)

CREATE SEQUENCE documents_versions_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;
CREATE TABLE app_documents_versions(
    documents_versions_id integer NOT NULL DEFAULT nextval('documents_versions_seq'::text),
    document_id integer NOT NULL,
    culture char(2) NOT NULL,
    version integer NOT NULL,
    document_archive_id integer NOT NULL,
    document_i18n_archive_id integer NOT NULL,
    history_metadata_id integer NOT NULL,  
    nature char(2) NOT NULL, -- nature of revision : either 'fo' = figures only, or 'to' = text only, or 'ft' = figures + text 
    -- "nature" field is filled by the functions triggered upon insertion on archive tables.
    created_at timestamp without time zone NOT NULL DEFAULT NOW()
);
-- maybe we could drop the implicit index created by the following primary key (not useful ?) ... 
ALTER TABLE app_documents_versions ADD CONSTRAINT documents_versions_pkey PRIMARY KEY (documents_versions_id);
ALTER TABLE app_documents_versions ADD CONSTRAINT fk_documents_versions_culture FOREIGN KEY (culture) REFERENCES app_cultures(culture);
ALTER TABLE app_documents_versions ADD CONSTRAINT fk_history_metadata FOREIGN KEY (history_metadata_id) REFERENCES app_history_metadata(history_metadata_id);

CREATE INDEX app_documents_versions_idx ON app_documents_versions USING btree (document_id, culture);
-- FIXME: add index on triple key (document_id, culture, version) ???

--
-- Name: documents_id_seq; Type: SEQUENCE; Its the sequence shared between all the documents
--
CREATE SEQUENCE documents_id_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 3 CACHE 1;
-- min value is 3 because guest user is 1 and c2c user is 2 (see db_users.sql)

-- a VIEW on the latest records of the documents_versions table
-- It is made to speed record searches in the exists_in_documents_versions function
-- It is not very clean, since it uses the number of languages managed by the site (currently 6) + 1 = 7 
CREATE VIEW latest_documents_versions_records AS SELECT * FROM app_documents_versions ORDER BY documents_versions_id DESC LIMIT 7 ;


-- The two following VIEWS are creating the "pseudo tables" containing the latest revision of every document. 

CREATE OR REPLACE VIEW documents AS SELECT a.id, a.module, a.is_protected, a.redirects_to, a.geom, a.geom_wkt, a.lon, a.lat, a.elevation FROM app_documents_archives a WHERE a.is_latest_version;

INSERT INTO "geometry_columns" VALUES ('','public','documents','geom',3,900913,'GEOMETRY');

CREATE OR REPLACE VIEW documents_i18n AS SELECT a.id, a.culture, a.name, a.search_name, a.description FROM app_documents_i18n_archives a WHERE a.is_latest_version;


-- these rules redirect to /dev/null any insert or delete requests on documents view 
CREATE OR REPLACE RULE update_documents AS ON UPDATE TO documents DO INSTEAD (); 
CREATE OR REPLACE RULE insert_documents AS ON INSERT TO documents DO INSTEAD (); 
CREATE OR REPLACE RULE delete_documents AS ON DELETE TO documents DO INSTEAD (); 


CREATE TABLE app_messages (
    culture char(2) NOT NULL,
    message character varying(400)
);
ALTER TABLE app_messages ADD CONSTRAINT messages_pkey PRIMARY KEY (culture); 
COMMENT ON TABLE app_messages IS 'Messages table : can be edited on site front page';

INSERT INTO app_messages (culture, message) VALUES ('ca', 'Welcome');
INSERT INTO app_messages (culture, message) VALUES ('de', 'Willkommen');
INSERT INTO app_messages (culture, message) VALUES ('en', 'Welcome. This text can be changed by moderators.');
INSERT INTO app_messages (culture, message) VALUES ('es', 'Recepción');
INSERT INTO app_messages (culture, message) VALUES ('fr', 'Bienvenue sur le nouveau site de Camptocamp.org');
INSERT INTO app_messages (culture, message) VALUES ('it', 'Benvenuto');



-- Table app_associations_log (the one that keeps track of manual associations history)

CREATE SEQUENCE associations_log_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;
CREATE TABLE app_associations_log(
    associations_log_id integer NOT NULL DEFAULT nextval('associations_log_seq'::text),
    main_id integer NOT NULL,
    linked_id integer NOT NULL,
    type char(2) NOT NULL, 
    user_id integer NOT NULL,
    is_creation boolean NOT NULL, -- true = association creation, false = association deletion
    written_at timestamp without time zone NOT NULL DEFAULT NOW()
);
ALTER TABLE app_associations_log ADD CONSTRAINT associations_log_pkey PRIMARY KEY (associations_log_id);
