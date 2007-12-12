-- Creates the db structure for parkings tables ...
--
-- $Id: db_parkings.sql 1066 2007-07-26 09:12:07Z alex $ --
--

-- Table app_parkings_archives --
CREATE SEQUENCE parkings_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_parkings_archives (
    parking_archive_id integer DEFAULT nextval('parkings_archives_seq'::text) NOT NULL,
    public_transportation_rating smallint,
    snow_clearance_rating smallint,
    lowest_elevation smallint
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_parkings_archives ADD CONSTRAINT parkings_archives_pkey PRIMARY KEY (parking_archive_id);
ALTER TABLE app_parkings_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 3);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_parkings_archives_id_idx ON app_parkings_archives USING btree (id); 
CREATE INDEX app_parkings_archives_geom_idx ON app_parkings_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_parkings_archives_redirects_idx ON app_parkings_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_parkings_archives_latest_idx ON app_parkings_archives USING btree (is_latest_version);
CREATE INDEX app_parkings_archives_document_archive_id_idx ON app_parkings_archives USING btree (document_archive_id);

-- Table app_parkings_i18n_archives --
CREATE SEQUENCE parkings_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_parkings_i18n_archives (
    parking_i18n_archive_id integer NOT NULL DEFAULT nextval('parkings_i18n_archives_seq'::text),
    public_transportation_description text,
    snow_clearance_comment text,
    accommodation text
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_parkings_i18n_archives ADD CONSTRAINT parkings_i18n_archives_pkey PRIMARY KEY (parking_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_parkings_i18n_archives_id_culture_idx ON app_parkings_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_parkings_i18n_archives_name_idx ON app_parkings_i18n_archives USING btree (search_name);
CREATE INDEX app_parkings_i18n_archives_latest_idx ON app_parkings_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_parkings_i18n_archives_document_i18n_archive_id_idx ON app_parkings_i18n_archives USING btree (document_i18n_archive_id);

-- Views --

CREATE OR REPLACE VIEW parkings AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.module, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt, sa.public_transportation_rating, sa.snow_clearance_rating, sa.lowest_elevation FROM app_parkings_archives sa WHERE sa.is_latest_version;
INSERT INTO "geometry_columns" VALUES ('','public','parkings','geom',3,900913,'POINT');

CREATE OR REPLACE VIEW parkings_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description, sa.public_transportation_description, sa.snow_clearance_comment, sa.accommodation FROM app_parkings_i18n_archives sa WHERE sa.is_latest_version;

-- Rules --

CREATE OR REPLACE RULE insert_parkings AS ON INSERT TO parkings DO INSTEAD
(
    INSERT INTO app_parkings_archives (id, module, is_protected, redirects_to, geom, geom_wkt, public_transportation_rating, snow_clearance_rating, lon, lat, elevation, lowest_elevation, is_latest_version) VALUES (NEW.id, 'parkings', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.public_transportation_rating, NEW.snow_clearance_rating, NEW.lon, NEW.lat, NEW.elevation, NEW.lowest_elevation, true)
);

CREATE OR REPLACE RULE update_parkings AS ON UPDATE TO parkings DO INSTEAD
(
    INSERT INTO app_parkings_archives (id, module, is_protected, redirects_to, geom, geom_wkt, public_transportation_rating, snow_clearance_rating, lon, lat, elevation, lowest_elevation, is_latest_version) VALUES (NEW.id, 'parkings', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.public_transportation_rating, NEW.snow_clearance_rating, NEW.lon, NEW.lat, NEW.elevation, NEW.lowest_elevation, true)
); 

CREATE OR REPLACE RULE insert_parkings_i18n AS ON INSERT TO parkings_i18n DO INSTEAD
(
    INSERT INTO app_parkings_i18n_archives (id, culture, name, search_name, description, public_transportation_description, snow_clearance_comment, accommodation, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.public_transportation_description, NEW.snow_clearance_comment, NEW.accommodation, true)
);

CREATE OR REPLACE RULE update_parkings_i18n AS ON UPDATE TO parkings_i18n DO INSTEAD
(
    INSERT INTO app_parkings_i18n_archives (id, culture, name, search_name, description, public_transportation_description, snow_clearance_comment, accommodation, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.public_transportation_description, NEW.snow_clearance_comment, NEW.accommodation, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_parkings_latest_version BEFORE INSERT ON app_parkings_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_parkings_archives);
CREATE TRIGGER update_parkings_i18n_latest_version BEFORE INSERT ON app_parkings_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_parkings_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_parkings_archives.... --
CREATE TRIGGER insert_parkings_i18n_archives AFTER INSERT ON app_parkings_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_parkings_archives AFTER INSERT ON app_parkings_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom_parkings BEFORE INSERT OR UPDATE ON app_parkings_archives FOR EACH ROW EXECUTE PROCEDURE update_geom_pt();

CREATE TRIGGER update_search_name_parkings_i18n_archives BEFORE INSERT OR UPDATE ON app_parkings_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
