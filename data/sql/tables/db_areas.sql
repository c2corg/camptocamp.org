-- Creates the db structure for areas tables ...
--
-- $Id: db_areas.sql 2247 2007-11-02 13:56:21Z alex $ --
--

-- Table app_areas_archives --
CREATE SEQUENCE areas_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_areas_archives (
    area_archive_id integer DEFAULT nextval('areas_archives_seq'::text) NOT NULL,
    area_type smallint
) INHERITS (app_documents_archives);

-- area_type : 
-- 1 = range
-- 2 = country
-- 3 = department

CREATE INDEX app_areas_archives_type_idx ON app_areas_archives USING btree (area_type); 

ALTER TABLE ONLY app_areas_archives ADD CONSTRAINT areas_archives_pkey PRIMARY KEY (area_archive_id);
ALTER TABLE app_areas_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 2);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_areas_archives_id_idx ON app_areas_archives USING btree (id); 
CREATE INDEX app_areas_archives_geom_idx ON app_areas_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_areas_archives_redirects_idx ON app_areas_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_areas_archives_latest_idx ON app_areas_archives USING btree (is_latest_version);
CREATE INDEX app_areas_archives_document_archive_id_idx ON app_areas_archives USING btree (document_archive_id);

-- Table app_areas_i18n_archives --
CREATE SEQUENCE areas_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_areas_i18n_archives (
    area_i18n_archive_id integer NOT NULL DEFAULT nextval('areas_i18n_archives_seq'::text) 
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_areas_i18n_archives ADD CONSTRAINT areas_i18n_archives_pkey PRIMARY KEY (area_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_areas_i18n_archives_id_culture_idx ON app_areas_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_areas_i18n_archives_name_idx ON app_areas_i18n_archives USING btree (search_name); 
CREATE INDEX app_areas_i18n_archives_latest_idx ON app_areas_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_areas_i18n_archives_document_i18n_archive_id_idx ON app_areas_i18n_archives USING btree (document_i18n_archive_id);

-- Views --
-- elevation of centroid not useful here but is present in view because declared as a property of all document objet in doctrine
CREATE OR REPLACE VIEW areas AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.area_type, sa.module, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt FROM app_areas_archives sa WHERE sa.is_latest_version; 
INSERT INTO "geometry_columns" VALUES ('','public','areas','geom',2,900913,'MULTIPOLYGON');

CREATE OR REPLACE VIEW areas_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description FROM app_areas_i18n_archives sa WHERE sa.is_latest_version;

-- Rules --

CREATE OR REPLACE RULE insert_areas AS ON INSERT TO areas DO INSTEAD 
(
    INSERT INTO app_areas_archives (id, module, is_protected, redirects_to, area_type, geom_wkt, geom, is_latest_version) VALUES (NEW.id, 'areas', NEW.is_protected, NEW.redirects_to, NEW.area_type, NEW.geom_wkt, NEW.geom, true)
);

CREATE OR REPLACE RULE update_areas AS ON UPDATE TO areas DO INSTEAD 
(
    INSERT INTO app_areas_archives (id, module, is_protected, redirects_to, area_type, geom_wkt, geom, is_latest_version, lon, lat) VALUES (NEW.id, 'areas', NEW.is_protected, NEW.redirects_to, NEW.area_type, NEW.geom_wkt, NEW.geom, true, NEW.lon, NEW.lat)
); 

CREATE OR REPLACE RULE insert_areas_i18n AS ON INSERT TO areas_i18n DO INSTEAD 
(
    INSERT INTO app_areas_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

CREATE OR REPLACE RULE update_areas_i18n AS ON UPDATE TO areas_i18n DO INSTEAD 
(
    INSERT INTO app_areas_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_areas_latest_version BEFORE INSERT ON app_areas_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_areas_archives);
CREATE TRIGGER update_areas_i18n_latest_version BEFORE INSERT ON app_areas_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_areas_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_areas_archives.... --
CREATE TRIGGER insert_areas_i18n_archives AFTER INSERT ON app_areas_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_areas_archives AFTER INSERT ON app_areas_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- function that updates the geom columns (wkt to/from wkb conversion) and simplifies WKT for representation purposes
-- used only for documents of type "area" (2D)
CREATE OR REPLACE FUNCTION update_areas_geom() RETURNS "trigger" AS
$BODY$
    DECLARE
        geomT geometry;
    BEGIN
        IF NEW.geom_wkt IS NULL AND NEW.geom IS NOT NULL THEN 
        -- this is the case when we want to delete a document's geometry (FIXME: should not be used on areas => remove code ?)
            NEW.geom:=null;
            NEW.lon:=null; -- of centroid
            NEW.lat:=null; -- of centroid
        END IF;
        IF NEW.geom IS NOT NULL THEN
        -- used only on area update to update geom_wkt, and centroid lon, lat
            NEW.geom_wkt:=AsText(Simplify(NEW.geom, 1000)); 
            geomT = Centroid(Transform(NEW.geom, 4326));
            NEW.lon:=X(geomT);
            NEW.lat:=Y(geomT);
        END IF;
        IF NEW.geom_wkt IS NOT NULL AND NEW.geom IS NULL THEN
        -- used only when importing data.
            NEW.geom:=Transform(GeometryFromText(NEW.geom_wkt, 4326), 900913); 
            NEW.geom_wkt:=AsText(Simplify(NEW.geom, 1000));
            geomT = Centroid(Transform(NEW.geom, 4326));
            NEW.lon:=X(geomT);
            NEW.lat:=Y(geomT);
        END IF;
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;


-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom_areas BEFORE INSERT OR UPDATE ON app_areas_archives FOR EACH ROW EXECUTE PROCEDURE update_areas_geom();

CREATE TRIGGER update_search_name_areas_i18n_archives BEFORE INSERT OR UPDATE ON app_areas_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
