-- Creates the db structure for maps tables ...
--
-- $Id: db_maps.sql 2247 2007-11-02 13:56:21Z alex $ --
--

-- Table app_maps_archives --
CREATE SEQUENCE maps_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_maps_archives (
    map_archive_id integer DEFAULT nextval('maps_archives_seq'::text) NOT NULL,
    editor smallint,
    scale integer,
    code varchar(20)
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_maps_archives ADD CONSTRAINT maps_archives_pkey PRIMARY KEY (map_archive_id);
ALTER TABLE app_maps_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 2);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_maps_archives_id_idx ON app_maps_archives USING btree (id); 
CREATE INDEX app_maps_archives_geom_idx ON app_maps_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_maps_archives_redirects_idx ON app_maps_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_maps_archives_latest_idx ON app_maps_archives USING btree (is_latest_version);
CREATE INDEX app_maps_archives_document_archive_id_idx ON app_maps_archives USING btree (document_archive_id);

-- Table app_maps_i18n_archives --
CREATE SEQUENCE maps_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_maps_i18n_archives (
    map_i18n_archive_id integer NOT NULL DEFAULT nextval('maps_i18n_archives_seq'::text) 
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_maps_i18n_archives ADD CONSTRAINT maps_i18n_archives_pkey PRIMARY KEY (map_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_maps_i18n_archives_id_culture_idx ON app_maps_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_maps_i18n_archives_name_idx ON app_maps_i18n_archives USING btree (search_name); 
CREATE INDEX app_maps_i18n_archives_latest_idx ON app_maps_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_maps_i18n_archives_document_i18n_archive_id_idx ON app_maps_i18n_archives USING btree (document_i18n_archive_id);

-- Views --
-- elevation of centroid not useful here but is present in view because declared as a property of all document objet in doctrine
CREATE OR REPLACE VIEW maps AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.editor, sa.scale, sa.code, sa.module, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt FROM app_maps_archives sa WHERE sa.is_latest_version; 
INSERT INTO "geometry_columns" VALUES ('','public','maps','geom',2,900913,'MULTIPOLYGON');

CREATE OR REPLACE VIEW maps_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description FROM app_maps_i18n_archives sa WHERE sa.is_latest_version;

-- Rules --

CREATE OR REPLACE RULE insert_maps AS ON INSERT TO maps DO INSTEAD 
(
    INSERT INTO app_maps_archives (id, module, is_protected, redirects_to, editor, scale, code, geom_wkt, geom, is_latest_version) VALUES (NEW.id, 'maps', NEW.is_protected, NEW.redirects_to, NEW.editor, NEW.scale, NEW.code, NEW.geom_wkt, NEW.geom, true)
);

CREATE OR REPLACE RULE update_maps AS ON UPDATE TO maps DO INSTEAD 
(
    INSERT INTO app_maps_archives (id, module, is_protected, redirects_to, editor, scale, code, geom_wkt, geom, is_latest_version, lon, lat) VALUES (NEW.id, 'maps', NEW.is_protected, NEW.redirects_to, NEW.editor, NEW.scale, NEW.code, NEW.geom_wkt, NEW.geom, true, NEW.lon, NEW.lat)
); 

CREATE OR REPLACE RULE insert_maps_i18n AS ON INSERT TO maps_i18n DO INSTEAD 
(
    INSERT INTO app_maps_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

CREATE OR REPLACE RULE update_maps_i18n AS ON UPDATE TO maps_i18n DO INSTEAD 
(
    INSERT INTO app_maps_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_maps_latest_version BEFORE INSERT ON app_maps_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_maps_archives);
CREATE TRIGGER update_maps_i18n_latest_version BEFORE INSERT ON app_maps_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_maps_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_maps_archives.... --
CREATE TRIGGER insert_maps_i18n_archives AFTER INSERT ON app_maps_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_maps_archives AFTER INSERT ON app_maps_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();


-- function that updates the geom columns (wkt to/from wkb conversion)
CREATE OR REPLACE FUNCTION update_maps_geom() RETURNS "trigger" AS
$BODY$
    DECLARE
        geomT geometry;
    BEGIN
        IF NEW.geom_wkt IS NULL AND NEW.geom IS NOT NULL THEN 
        -- this is the case when we want to delete a document's geometry
            NEW.geom:=null;
            NEW.lon:=null; -- of centroid
            NEW.lat:=null; -- of centroid
        END IF;
        IF NEW.geom IS NOT NULL THEN
        -- used only on map update to update geom_wkt, centroid lon and lat and ele
            NEW.geom_wkt:=AsText(NEW.geom);
            geomT = Centroid(Transform(NEW.geom, 4326));
            NEW.lon:=X(geomT);
            NEW.lat:=Y(geomT);
        END IF;
        IF NEW.geom_wkt IS NOT NULL AND NEW.geom IS NULL THEN
        -- used only when importing data.
            NEW.geom:=Transform(GeometryFromText(NEW.geom_wkt, 4326), 900913);
            NEW.geom_wkt:=AsText(NEW.geom); -- warning : this is a 2D WKT
            geomT = Centroid(Transform(NEW.geom, 4326));
            NEW.lon:=X(geomT);
            NEW.lat:=Y(geomT);
        END IF;
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;


-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom_maps BEFORE INSERT OR UPDATE ON app_maps_archives FOR EACH ROW EXECUTE PROCEDURE update_maps_geom();

CREATE TRIGGER update_search_name_maps_i18n_archives BEFORE INSERT OR UPDATE ON app_maps_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
