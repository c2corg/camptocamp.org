-- Creates the db structure for summits tables ...
--
-- $Id: db_summits.sql 2247 2007-11-02 13:56:21Z alex $ --
--

-- Table app_summits_archives --
CREATE SEQUENCE summits_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_summits_archives (
    summit_archive_id integer DEFAULT nextval('summits_archives_seq'::text) NOT NULL,
    summit_type smallint,
    maps_info varchar(300),
    v4_id smallint
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_summits_archives ADD CONSTRAINT summits_archives_pkey PRIMARY KEY (summit_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_summits_archives_id_idx ON app_summits_archives USING btree (id); 
CREATE INDEX app_summits_archives_geom_idx ON app_summits_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_summits_archives_redirects_idx ON app_summits_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_summits_archives_elevation_idx ON app_summits_archives USING btree (elevation);
ALTER TABLE app_summits_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 3);

CREATE INDEX app_summits_archives_v4_idx ON app_summits_archives USING btree (v4_id);
CREATE INDEX app_summits_archives_latest_idx ON app_summits_archives USING btree (is_latest_version);
CREATE INDEX app_summits_archives_document_archive_id_idx ON app_summits_archives USING btree (document_archive_id);

-- Table app_summits_i18n_archives --
CREATE SEQUENCE summits_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_summits_i18n_archives (
    summit_i18n_archive_id integer NOT NULL DEFAULT nextval('summits_i18n_archives_seq'::text) 
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_summits_i18n_archives ADD CONSTRAINT summits_i18n_archives_pkey PRIMARY KEY (summit_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_summits_i18n_archives_id_culture_idx ON app_summits_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_summits_i18n_archives_name_idx ON app_summits_i18n_archives USING btree (search_name); 
CREATE INDEX app_summits_i18n_archives_latest_idx ON app_summits_i18n_archives USING btree (is_latest_version); 
CREATE INDEX app_summits_i18n_archives_document_i18n_archive_id_idx ON app_summits_i18n_archives USING btree (document_i18n_archive_id);

-- Views --
CREATE OR REPLACE VIEW summits AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.summit_type, sa.v4_id, sa.maps_info, sa.module, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt FROM app_summits_archives sa WHERE sa.is_latest_version; 
INSERT INTO "geometry_columns" VALUES ('','public','summits','geom',3,900913,'POINT');

CREATE OR REPLACE VIEW summits_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description FROM app_summits_i18n_archives sa WHERE sa.is_latest_version;


-- Rules --
CREATE OR REPLACE RULE insert_summits AS ON INSERT TO summits DO INSTEAD 
(
    INSERT INTO app_summits_archives (id, module, is_protected, redirects_to, lon, lat, elevation, summit_type, maps_info, geom_wkt, geom, v4_id, is_latest_version) VALUES (NEW.id, 'summits', NEW.is_protected, NEW.redirects_to, NEW.lon, NEW.lat, NEW.elevation, NEW.summit_type, NEW.maps_info, NEW.geom_wkt, NEW.geom, NEW.v4_id, true)
);

CREATE OR REPLACE RULE update_summits AS ON UPDATE TO summits DO INSTEAD 
(
    INSERT INTO app_summits_archives (id, module, is_protected, redirects_to, lon, lat, elevation, summit_type, maps_info, geom_wkt, geom, v4_id, is_latest_version) VALUES (NEW.id, 'summits', NEW.is_protected, NEW.redirects_to, NEW.lon, NEW.lat, NEW.elevation, NEW.summit_type, NEW.maps_info, NEW.geom_wkt, NEW.geom, NEW.v4_id, true)
); 

CREATE OR REPLACE RULE insert_summits_i18n AS ON INSERT TO summits_i18n DO INSTEAD 
(
    INSERT INTO app_summits_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

CREATE OR REPLACE RULE update_summits_i18n AS ON UPDATE TO summits_i18n DO INSTEAD 
(
    INSERT INTO app_summits_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_summits_latest_version BEFORE INSERT ON app_summits_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_summits_archives);
CREATE TRIGGER update_summits_i18n_latest_version BEFORE INSERT ON app_summits_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_summits_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_summits_archives.... --
CREATE TRIGGER insert_summits_i18n_archives AFTER INSERT ON app_summits_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_summits_archives AFTER INSERT ON app_summits_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom_summits BEFORE INSERT OR UPDATE ON app_summits_archives FOR EACH ROW EXECUTE PROCEDURE update_geom_pt();

CREATE TRIGGER update_search_name_summits_i18n_archives BEFORE INSERT OR UPDATE ON app_summits_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
