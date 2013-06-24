-- Creates the db structure for images tables ...
--
-- $Id: db_images.sql 2333 2007-11-13 16:58:23Z alex $ --
--

-- Table app_images_archives --
CREATE SEQUENCE images_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_images_archives (
    image_archive_id integer DEFAULT nextval('images_archives_seq'::text) NOT NULL,
    filename character varying(30),
    has_svg boolean DEFAULT false,
    width smallint,
    height smallint,
    file_size integer,
    date_time timestamp without time zone,
    camera_name character varying(100),
    exposure_time numeric(6,4),
    focal_length numeric(5,1),
    fnumber numeric(3,1),
    iso_speed smallint,
    categories smallint[],
    activities smallint[],
    author varchar(100),
    image_type smallint,
    v4_id smallint,
    v4_app varchar(3)
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_images_archives ADD CONSTRAINT images_archives_pkey PRIMARY KEY (image_archive_id);
ALTER TABLE app_images_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 3);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_images_archives_id_idx ON app_images_archives USING btree (id); 
CREATE INDEX app_images_archives_geom_idx ON app_images_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_images_archives_redirects_idx ON app_images_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_images_archives_v4_idx ON app_images_archives USING btree (v4_id, v4_app); -- useful for filtering on va application (migrated contents)
CREATE INDEX app_images_archives_latest_idx ON app_images_archives USING btree (is_latest_version);
CREATE INDEX app_images_archives_document_archive_id_idx ON app_images_archives USING btree (document_archive_id);

-- Table app_images_i18n_archives --
CREATE SEQUENCE images_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_images_i18n_archives (
    image_i18n_archive_id integer NOT NULL DEFAULT nextval('images_i18n_archives_seq'::text) 
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_images_i18n_archives ADD CONSTRAINT images_i18n_archives_pkey PRIMARY KEY (image_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_images_i18n_archives_id_culture_idx ON app_images_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_images_i18n_archives_name_idx ON app_images_i18n_archives USING btree (search_name); 
CREATE INDEX app_images_i18n_archives_latest_idx ON app_images_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_images_i18n_archives_document_i18n_archive_id_idx ON app_images_i18n_archives USING btree (document_i18n_archive_id);

-- Views --

CREATE OR REPLACE VIEW images AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.module, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt, sa.filename, sa.has_svg, sa.width, sa.height, sa.file_size, sa.v4_id, sa.v4_app, sa.categories, sa.activities, sa.author, sa.date_time, sa.camera_name, sa.exposure_time, sa.fnumber, sa.iso_speed, sa.focal_length, sa.image_type FROM app_images_archives sa WHERE sa.is_latest_version; 

INSERT INTO "geometry_columns" VALUES ('','public','images','geom',3,900913,'POINT');

CREATE OR REPLACE VIEW images_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description FROM app_images_i18n_archives sa WHERE sa.is_latest_version;

-- Rules --

-- lon, lat, elevation useful for georeferencing
CREATE OR REPLACE RULE insert_images AS ON INSERT TO images DO INSTEAD 
(
    INSERT INTO app_images_archives (id, module, is_protected, redirects_to, geom, geom_wkt, lon, lat, elevation, filename, has_svg, width, height, file_size, categories, activities, author, date_time, camera_name, exposure_time, fnumber, iso_speed, focal_length, image_type, v4_id, v4_app, is_latest_version) VALUES (NEW.id, 'images', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.lon, NEW.lat, NEW.elevation, NEW.filename, NEW.has_svg, NEW.width, NEW.height, NEW.file_size, NEW.categories, NEW.activities, NEW.author, NEW.date_time, NEW.camera_name, NEW.exposure_time, NEW.fnumber, NEW.iso_speed, NEW.focal_length, NEW.image_type, NEW.v4_id, NEW.v4_app, true)
);

CREATE OR REPLACE RULE update_images AS ON UPDATE TO images DO INSTEAD 
(
    INSERT INTO app_images_archives (id, module, is_protected, redirects_to, geom, geom_wkt, lon, lat, elevation, filename, has_svg, width, height, file_size, categories, activities, author, date_time, camera_name, exposure_time, fnumber, iso_speed, focal_length, image_type, v4_id, v4_app, is_latest_version) VALUES (NEW.id, 'images', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.lon, NEW.lat, NEW.elevation, NEW.filename, NEW.has_svg,  NEW.width, NEW.height, NEW.file_size, NEW.categories, NEW.activities, NEW.author, NEW.date_time, NEW.camera_name, NEW.exposure_time, NEW.fnumber, NEW.iso_speed, NEW.focal_length, NEW.image_type, NEW.v4_id, NEW.v4_app, true)
); 

CREATE OR REPLACE RULE insert_images_i18n AS ON INSERT TO images_i18n DO INSTEAD 
(
    INSERT INTO app_images_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

CREATE OR REPLACE RULE update_images_i18n AS ON UPDATE TO images_i18n DO INSTEAD 
(
    INSERT INTO app_images_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_images_latest_version BEFORE INSERT ON app_images_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_images_archives);
CREATE TRIGGER update_images_i18n_latest_version BEFORE INSERT ON app_images_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_images_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_images_archives.... --
CREATE TRIGGER insert_images_i18n_archives AFTER INSERT ON app_images_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_images_archives AFTER INSERT ON app_images_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom_images BEFORE INSERT OR UPDATE ON app_images_archives FOR EACH ROW EXECUTE PROCEDURE update_geom_pt();

CREATE TRIGGER update_search_name_images_i18n_archives BEFORE INSERT OR UPDATE ON app_images_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
