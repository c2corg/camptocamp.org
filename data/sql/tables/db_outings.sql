-- Creates the db structure for outings tables ...
--
-- $Id: db_outings.sql 2247 2007-11-02 13:56:21Z alex $ --
--


-- Table app_outings_archives --
CREATE SEQUENCE outings_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_outings_archives (
    outing_archive_id integer DEFAULT nextval('outings_archives_seq'::text) NOT NULL,
    date date,
    activities smallint[],
    height_diff_up smallint,
    height_diff_down smallint,
    -- The 3 following fields are updated only when geom exists (via uploaded KML or GPX)
    outing_length integer, -- in meters. 
    min_elevation smallint, -- in meters
    max_elevation smallint, -- in meters
    partial_trip boolean,
    hut_status smallint,
    frequentation_status smallint,
    conditions_status smallint,
    access_status smallint,
    access_elevation smallint,
    lift_status smallint,
    glacier_status smallint,
    up_snow_elevation smallint,
    down_snow_elevation smallint,
    track_status smallint,
    outing_with_public_transportation boolean,
    avalanche_date smallint[],
    v4_id smallint,
    v4_app varchar(3)
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_outings_archives ADD CONSTRAINT outings_archives_pkey PRIMARY KEY (outing_archive_id);
ALTER TABLE app_outings_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 4);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_outings_archives_id_idx ON app_outings_archives USING btree (id); 
CREATE INDEX app_outings_archives_geom_idx ON app_outings_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_outings_archives_redirects_idx ON app_outings_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_outings_archives_v4_idx ON app_outings_archives USING btree (v4_id, v4_app); -- useful for filtering on v4 application (migrated contents)
CREATE INDEX app_outings_archives_latest_idx ON app_outings_archives USING btree (is_latest_version);
CREATE INDEX app_outings_archives_id_date_idx ON app_outings_archives USING btree (date, id);
CREATE INDEX app_outings_archives_document_archive_id_idx ON app_outings_archives USING btree (document_archive_id);

-- Table app_outings_i18n_archives --
CREATE SEQUENCE outings_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_outings_i18n_archives (
    outing_i18n_archive_id integer NOT NULL DEFAULT nextval('outings_i18n_archives_seq'::text),
    participants text,
    timing text,
    weather text,
    hut_comments text,
    access_comments text,
    conditions text,
    conditions_levels text,
    avalanche_desc text,
    outing_route_desc text
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_outings_i18n_archives ADD CONSTRAINT outings_i18n_archives_pkey PRIMARY KEY (outing_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_outings_i18n_archives_id_culture_idx ON app_outings_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_outings_i18n_archives_name_idx ON app_outings_i18n_archives USING btree (search_name); 
CREATE INDEX app_outings_i18n_archives_latest_idx ON app_outings_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_outings_i18n_archives_document_i18n_archive_id_idx ON app_outings_i18n_archives USING btree (document_i18n_archive_id);

-- Views --
-- mean elevation useful here
CREATE OR REPLACE VIEW outings AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.module, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt, sa.date, sa.activities, sa.height_diff_up, sa.height_diff_down, sa.outing_length, min_elevation, sa.max_elevation, sa.partial_trip, sa.hut_status, sa.frequentation_status, sa.conditions_status, sa.access_status, sa.access_elevation, sa.lift_status, sa.glacier_status, sa.up_snow_elevation, sa.down_snow_elevation, sa.track_status, sa.outing_with_public_transportation, sa.avalanche_date, sa.v4_id, sa.v4_app FROM app_outings_archives sa WHERE sa.is_latest_version; 

INSERT INTO "geometry_columns" VALUES ('','public','outings','geom',4,900913,'LINESTRING');

CREATE OR REPLACE VIEW outings_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description, sa.participants, sa.weather, sa.hut_comments, sa.access_comments, sa.conditions, sa.conditions_levels, sa.timing, sa.avalanche_desc, sa.outing_route_desc FROM app_outings_i18n_archives sa WHERE sa.is_latest_version;

-- Rules --

CREATE OR REPLACE RULE insert_outings AS ON INSERT TO outings DO INSTEAD 
(
    INSERT INTO app_outings_archives (id, module, is_protected, redirects_to, geom, geom_wkt, date, activities, partial_trip, hut_status, frequentation_status, conditions_status, access_status, access_elevation, lift_status, glacier_status, up_snow_elevation, down_snow_elevation, track_status, outing_with_public_transportation, height_diff_up, height_diff_down, max_elevation, outing_length, avalanche_date, v4_id, v4_app, is_latest_version) VALUES (NEW.id, 'outings', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.date, NEW.activities, NEW.partial_trip, NEW.hut_status, NEW.frequentation_status, NEW.conditions_status, NEW.access_status, NEW.access_elevation, NEW.lift_status, NEW.glacier_status, NEW.up_snow_elevation, NEW.down_snow_elevation, NEW.track_status, NEW.outing_with_public_transportation, NEW.height_diff_up, NEW.height_diff_down, NEW.max_elevation, NEW.outing_length, NEW.avalanche_date, NEW.v4_id, NEW.v4_app, true)
);

CREATE OR REPLACE RULE update_outings AS ON UPDATE TO outings DO INSTEAD 
(
    INSERT INTO app_outings_archives (id, module, is_protected, redirects_to, geom, geom_wkt, date, activities, partial_trip, hut_status, frequentation_status, conditions_status, access_status, access_elevation, lift_status, glacier_status, up_snow_elevation, down_snow_elevation, track_status, outing_with_public_transportation, height_diff_up, height_diff_down, max_elevation, outing_length, avalanche_date, v4_id, v4_app, is_latest_version) VALUES (NEW.id, 'outings', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.date, NEW.activities, NEW.partial_trip, NEW.hut_status, NEW.frequentation_status, NEW.conditions_status, NEW.access_status, NEW.access_elevation, NEW.lift_status, NEW.glacier_status, NEW.up_snow_elevation, NEW.down_snow_elevation, NEW.track_status, NEW.outing_with_public_transportation, NEW.height_diff_up, NEW.height_diff_down, NEW.max_elevation, NEW.outing_length, NEW.avalanche_date, NEW.v4_id, NEW.v4_app, true)
); 

CREATE OR REPLACE RULE insert_outings_i18n AS ON INSERT TO outings_i18n DO INSTEAD 
(
    INSERT INTO app_outings_i18n_archives (id, culture, name, search_name, description, participants, weather, hut_comments, access_comments, conditions, conditions_levels, timing, avalanche_desc, outing_route_desc, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.participants, NEW.weather, NEW.hut_comments, NEW.access_comments, NEW.conditions, NEW.conditions_levels, NEW.timing, NEW.avalanche_desc, NEW.outing_route_desc, true)
);

CREATE OR REPLACE RULE update_outings_i18n AS ON UPDATE TO outings_i18n DO INSTEAD 
(
    INSERT INTO app_outings_i18n_archives (id, culture, name, search_name, description, participants, weather, hut_comments, access_comments, conditions, conditions_levels, timing, avalanche_desc, outing_route_desc, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.participants, NEW.weather, NEW.hut_comments, NEW.access_comments, NEW.conditions, NEW.conditions_levels, NEW.timing, NEW.avalanche_desc, NEW.outing_route_desc, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_outings_latest_version BEFORE INSERT ON app_outings_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_outings_archives);
CREATE TRIGGER update_outings_i18n_latest_version BEFORE INSERT ON app_outings_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_outings_i18n_archives);

CREATE TRIGGER insert_outings_i18n_archives AFTER INSERT ON app_outings_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();

CREATE TRIGGER insert_outings_archives AFTER INSERT ON app_outings_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- function that updates the geom columns (wkt to/from wkb conversion)
-- NB: it has to handle 3D and 4D geometries
CREATE OR REPLACE FUNCTION update_outing_geom() RETURNS "trigger" AS
$BODY$
    DECLARE
        geomT geometry;
        b box3d;
        i smallint;
    BEGIN
        IF NEW.geom_wkt IS NULL AND NEW.geom IS NOT NULL THEN -- this is the case when we want to delete a document's geometry
            NEW.geom:=null;
            NEW.lon:=null; -- of centroid
            NEW.lat:=null; -- of centroid
            NEW.elevation:=null; -- of centroid
        END IF;
        IF NEW.geom_wkt IS NOT NULL AND ndims(NEW.geom_wkt) > 2 THEN -- new data is entered via a WKT
            NEW.geom:=Transform(GeometryFromText(NEW.geom_wkt, 4326), 900913);  -- storage of 3D 4326 WKT into a 3D 900913 WKB
            NEW.geom_wkt:=AsText(NEW.geom); -- conversion of 3 or 4D 900913 WKB into 2D 900913 WKT (loss of information !)
        END IF;
        -- warning : at this point, NEW.geom_wkt is always a 2D WKT, because asText does not handle 3D nor 4D. 
        -- Since it is used for representation purposes (OpenLayers), this is no pb.
        IF NEW.geom IS NOT NULL THEN
            geomT = Centroid(Transform(NEW.geom, 4326));
            NEW.lon:=X(geomT); -- of centroid
            NEW.lat:=Y(geomT); -- of centroid
            NEW.elevation:=Z(geomT); -- of centroid
            -- we automatically update the following fields from the geom generated via GPX or KML upload:
            NEW.outing_length := round(ST_length(ST_Transform(NEW.geom,4326),true));
            b = box3D(NEW.geom);
            i := round(zmin(b));
            IF i > 0 THEN
                NEW.min_elevation := i;
            END IF;
            i := round(zmax(b));
            IF i > 0 THEN
                NEW.max_elevation := i;
            END IF;
        END IF;
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom_outings BEFORE INSERT OR UPDATE ON app_outings_archives FOR EACH ROW EXECUTE PROCEDURE update_outing_geom();

CREATE TRIGGER update_search_name_outings_i18n_archives BEFORE INSERT OR UPDATE ON app_outings_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
