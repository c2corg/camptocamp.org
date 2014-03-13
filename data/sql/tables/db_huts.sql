-- Creates the db structure for huts tables ...
--
-- $Id: db_huts.sql 1066 2007-07-26 09:12:07Z alex $ --
--
 

-- Table app_huts_archives --
CREATE SEQUENCE huts_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_huts_archives (
    hut_archive_id integer DEFAULT nextval('huts_archives_seq'::text) NOT NULL,
    shelter_type smallint,
    is_staffed boolean,
    phone varchar(50),
    url varchar(255),
    staffed_capacity smallint,
    unstaffed_capacity smallint,
    has_unstaffed_matress smallint,
    has_unstaffed_blanket smallint,
    has_unstaffed_gas smallint, 
    has_unstaffed_wood smallint,
    activities smallint[]
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_huts_archives ADD CONSTRAINT huts_archives_pkey PRIMARY KEY (hut_archive_id);
ALTER TABLE app_huts_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 3);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_huts_archives_id_idx ON app_huts_archives USING btree (id); 
CREATE INDEX app_huts_archives_geom_idx ON app_huts_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_huts_archives_redirects_idx ON app_huts_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_huts_archives_latest_idx ON app_huts_archives USING btree (is_latest_version);
CREATE INDEX app_huts_archives_document_archive_id_idx ON app_huts_archives USING btree (document_archive_id);

-- Table app_huts_i18n_archives --
CREATE SEQUENCE huts_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_huts_i18n_archives (
    hut_i18n_archive_id integer NOT NULL DEFAULT nextval('huts_i18n_archives_seq'::text),
    pedestrian_access text,
    staffed_period varchar(200)
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_huts_i18n_archives ADD CONSTRAINT huts_i18n_archives_pkey PRIMARY KEY (hut_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_huts_i18n_archives_id_culture_idx ON app_huts_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_huts_i18n_archives_name_idx ON app_huts_i18n_archives USING btree (search_name); 
CREATE INDEX app_huts_i18n_archives_latest_idx ON app_huts_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_huts_i18n_archives_document_i18n_archive_id_idx ON app_huts_i18n_archives USING btree (document_i18n_archive_id);

-- Views --

CREATE OR REPLACE VIEW huts AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.module, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt, sa.shelter_type, sa.is_staffed, sa.phone, sa.url, sa.staffed_capacity, sa.unstaffed_capacity, sa.has_unstaffed_matress, sa.has_unstaffed_blanket, sa.has_unstaffed_gas, sa.has_unstaffed_wood, sa.activities FROM app_huts_archives sa WHERE sa.is_latest_version;
INSERT INTO "geometry_columns" VALUES ('','public','huts','geom',3,900913,'POINT');

CREATE OR REPLACE VIEW huts_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description, sa.pedestrian_access, sa.staffed_period FROM app_huts_i18n_archives sa WHERE sa.is_latest_version;

-- Rules --

CREATE OR REPLACE RULE insert_huts AS ON INSERT TO huts DO INSTEAD
(
    INSERT INTO app_huts_archives (id, module, is_protected, redirects_to, geom, geom_wkt, lon, lat, elevation, shelter_type, is_staffed, phone, url, staffed_capacity, unstaffed_capacity, has_unstaffed_matress, has_unstaffed_blanket, has_unstaffed_gas, has_unstaffed_wood, activities, is_latest_version) VALUES (NEW.id, 'huts', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.lon, NEW.lat, NEW.elevation, NEW.shelter_type, NEW.is_staffed, NEW.phone, NEW.url, NEW.staffed_capacity, NEW.unstaffed_capacity, NEW.has_unstaffed_matress, NEW.has_unstaffed_blanket, NEW.has_unstaffed_gas, NEW.has_unstaffed_wood, NEW.activities, true)
);

CREATE OR REPLACE RULE update_huts AS ON UPDATE TO huts DO INSTEAD
(
    INSERT INTO app_huts_archives (id, module, is_protected, redirects_to, geom, geom_wkt, lon, lat, elevation, shelter_type, is_staffed, phone, url, staffed_capacity, unstaffed_capacity, has_unstaffed_matress, has_unstaffed_blanket, has_unstaffed_gas, has_unstaffed_wood, activities, is_latest_version) VALUES (NEW.id, 'huts', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.lon, NEW.lat, NEW.elevation, NEW.shelter_type, NEW.is_staffed, NEW.phone, NEW.url, NEW.staffed_capacity, NEW.unstaffed_capacity, NEW.has_unstaffed_matress, NEW.has_unstaffed_blanket, NEW.has_unstaffed_gas, NEW.has_unstaffed_wood, NEW.activities, true)
); 

CREATE OR REPLACE RULE insert_huts_i18n AS ON INSERT TO huts_i18n DO INSTEAD
(
    INSERT INTO app_huts_i18n_archives (id, culture, name, search_name, description, pedestrian_access, staffed_period, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.pedestrian_access, NEW.staffed_period, true)
);

CREATE OR REPLACE RULE update_huts_i18n AS ON UPDATE TO huts_i18n DO INSTEAD
(
    INSERT INTO app_huts_i18n_archives (id, culture, name, search_name, description, pedestrian_access, staffed_period, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.pedestrian_access, NEW.staffed_period, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_huts_latest_version BEFORE INSERT ON app_huts_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_huts_archives);
CREATE TRIGGER update_huts_i18n_latest_version BEFORE INSERT ON app_huts_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_huts_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_huts_archives.... --
CREATE TRIGGER insert_huts_i18n_archives AFTER INSERT ON app_huts_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_huts_archives AFTER INSERT ON app_huts_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom BEFORE INSERT OR UPDATE ON app_huts_archives FOR EACH ROW EXECUTE PROCEDURE update_geom_pt();

CREATE TRIGGER update_search_name_huts_i18n_archives BEFORE INSERT OR UPDATE ON app_huts_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
