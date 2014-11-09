-- Creates the db structure for routes tables ...
--
-- $Id: db_routes.sql 2247 2007-11-02 13:56:21Z alex $ --
--

-- Table app_routes_archives --
CREATE SEQUENCE routes_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_routes_archives (
    route_archive_id integer DEFAULT nextval('routes_archives_seq'::text) NOT NULL,
    activities smallint[],
    facing smallint,
    height_diff_up smallint,
    height_diff_down smallint,
    route_type smallint,
    -- The 3 following fields are updated only when geom exists (via uploaded KML or GPX) 
    route_length integer,  -- in meters
    min_elevation smallint, -- in meters
    max_elevation smallint, -- in meters
    duration smallint,
    slope varchar(100),
    difficulties_height smallint,
    configuration smallint[],
    global_rating smallint,
    engagement_rating smallint,
    objective_risk_rating smallint,
    equipment_rating smallint,
    is_on_glacier boolean,
    sub_activities smallint[],
    toponeige_technical_rating smallint,
    toponeige_exposition_rating smallint,
    labande_ski_rating smallint,
    labande_global_rating smallint,
    ice_rating smallint,
    mixed_rating smallint,
    rock_free_rating smallint,
    rock_required_rating smallint,
    aid_rating smallint,
    rock_exposition_rating smallint,
    snowshoeing_rating smallint
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_routes_archives ADD CONSTRAINT routes_archives_pkey PRIMARY KEY (route_archive_id);
ALTER TABLE app_routes_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 3);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_routes_archives_id_idx ON app_routes_archives USING btree (id); 
CREATE INDEX app_routes_archives_geom_idx ON app_routes_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_routes_archives_redirects_idx ON app_routes_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_routes_archives_latest_idx ON app_routes_archives USING btree (is_latest_version);
CREATE INDEX app_routes_archives_document_archive_id_idx ON app_routes_archives USING btree (document_archive_id);

-- Table app_routes_i18n_archives --
CREATE SEQUENCE routes_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_routes_i18n_archives (
    route_i18n_archive_id integer NOT NULL DEFAULT nextval('routes_i18n_archives_seq'::text),
    remarks text,
    gear text,
    external_resources text,
    route_history text,
    v4_id smallint,
    v4_app varchar(3)
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_routes_i18n_archives ADD CONSTRAINT routes_i18n_archives_pkey PRIMARY KEY (route_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_routes_i18n_archives_culture_idx ON app_routes_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_routes_i18n_archives_name_idx ON app_routes_i18n_archives USING btree (search_name); 
CREATE INDEX app_routes_i18n_archives_v4_idx ON app_routes_i18n_archives USING btree (v4_id, v4_app);
CREATE INDEX app_routes_i18n_archives_latest_idx ON app_routes_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_routes_i18n_archives_document_i18n_archive_id_idx ON app_routes_i18n_archives USING btree (document_i18n_archive_id);

-- Views --

CREATE OR REPLACE VIEW routes AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.module, sa.activities, sa.facing, sa.height_diff_up, sa.height_diff_down, sa.route_type, sa.route_length, sa.min_elevation, sa.max_elevation, sa.duration, sa.slope, sa.difficulties_height, sa.configuration, sa.global_rating, sa.engagement_rating, sa.objective_risk_rating, sa.equipment_rating, sa.is_on_glacier, sa.sub_activities, sa.toponeige_technical_rating, sa.toponeige_exposition_rating, sa.labande_ski_rating, sa.labande_global_rating, sa.ice_rating, sa.mixed_rating, sa.rock_free_rating, sa.rock_required_rating, sa.aid_rating, sa.rock_exposition_rating, sa.hiking_rating, sa.snowshoeing_rating, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt FROM app_routes_archives sa WHERE sa.is_latest_version; 
INSERT INTO "geometry_columns" VALUES ('','public','routes','geom',3,900913,'LINESTRING');

CREATE OR REPLACE VIEW routes_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description, sa.remarks, sa.gear, sa.external_resources, sa.route_history, sa.v4_id, sa.v4_app FROM app_routes_i18n_archives sa WHERE sa.is_latest_version;

-- Rules --

CREATE OR REPLACE RULE insert_routes AS ON INSERT TO routes DO INSTEAD 
(
    INSERT INTO app_routes_archives (id, module, is_protected, redirects_to, activities, facing, height_diff_up, height_diff_down, route_type, duration, slope, difficulties_height, configuration, global_rating, engagement_rating, objective_risk_rating, equipment_rating, is_on_glacier, sub_activities, toponeige_technical_rating, toponeige_exposition_rating, labande_ski_rating, labande_global_rating, ice_rating, mixed_rating, rock_free_rating, rock_required_rating, aid_rating, rock_exposition_rating, hiking_rating, snowshoeing_rating, geom, geom_wkt, is_latest_version, min_elevation, max_elevation, elevation, route_length) VALUES (NEW.id, 'routes', NEW.is_protected, NEW.redirects_to, NEW.activities, NEW.facing, NEW.height_diff_up, NEW.height_diff_down, NEW.route_type, NEW.duration, NEW.slope, NEW.difficulties_height, NEW.configuration, NEW.global_rating, NEW.engagement_rating, NEW.objective_risk_rating, NEW.equipment_rating, NEW.is_on_glacier, NEW.sub_activities, NEW.toponeige_technical_rating, NEW.toponeige_exposition_rating, NEW.labande_ski_rating, NEW.labande_global_rating, NEW.ice_rating, NEW.mixed_rating, NEW.rock_free_rating, NEW.rock_required_rating, NEW.aid_rating, NEW.rock_exposition_rating, NEW.hiking_rating, NEW.snowshoeing_rating, NEW.geom, NEW.geom_wkt, true, NEW.min_elevation, NEW.max_elevation, NEW.elevation, NEW.route_length)
);

CREATE OR REPLACE RULE update_routes AS ON UPDATE TO routes DO INSTEAD 
(
    INSERT INTO app_routes_archives (id, module, is_protected, redirects_to, activities, facing, height_diff_up, height_diff_down, route_type, duration, slope, difficulties_height, configuration, global_rating, engagement_rating, objective_risk_rating, equipment_rating, is_on_glacier, sub_activities, toponeige_technical_rating, toponeige_exposition_rating, labande_ski_rating, labande_global_rating, ice_rating, mixed_rating, rock_free_rating, rock_required_rating, aid_rating, rock_exposition_rating, hiking_rating, snowshoeing_rating, geom, geom_wkt, is_latest_version, min_elevation, max_elevation, elevation, route_length) VALUES (NEW.id, 'routes', NEW.is_protected, NEW.redirects_to, NEW.activities, NEW.facing, NEW.height_diff_up, NEW.height_diff_down, NEW.route_type, NEW.duration, NEW.slope, NEW.difficulties_height, NEW.configuration, NEW.global_rating, NEW.engagement_rating, NEW.objective_risk_rating, NEW.equipment_rating, NEW.is_on_glacier, NEW.sub_activities, NEW.toponeige_technical_rating, NEW.toponeige_exposition_rating, NEW.labande_ski_rating, NEW.labande_global_rating, NEW.ice_rating, NEW.mixed_rating, NEW.rock_free_rating, NEW.rock_required_rating, NEW.aid_rating, NEW.rock_exposition_rating, NEW.hiking_rating, NEW.snowshoeing_rating, NEW.geom, NEW.geom_wkt, true, NEW.min_elevation, NEW.max_elevation, NEW.elevation, NEW.route_length)
); 

CREATE OR REPLACE RULE insert_routes_i18n AS ON INSERT TO routes_i18n DO INSTEAD 
(
    INSERT INTO app_routes_i18n_archives (id, culture, name, search_name, description, remarks, gear, external_resources, route_history, v4_id, v4_app, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.remarks, NEW.gear, NEW.external_resources, NEW.route_history, NEW.v4_id, NEW.v4_app, true)
);

CREATE OR REPLACE RULE update_routes_i18n AS ON UPDATE TO routes_i18n DO INSTEAD 
(
    INSERT INTO app_routes_i18n_archives (id, culture, name, search_name, description, remarks, gear, external_resources, route_history, v4_id, v4_app, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.remarks, NEW.gear, NEW.external_resources, NEW.route_history, NEW.v4_id, NEW.v4_app, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_routes_latest_version BEFORE INSERT ON app_routes_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_routes_archives);
CREATE TRIGGER update_routes_i18n_latest_version BEFORE INSERT ON app_routes_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_routes_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_routes_archives.... --
CREATE TRIGGER insert_routes_i18n_archives AFTER INSERT ON app_routes_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_routes_archives AFTER INSERT ON app_routes_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();


-- function that updates the geom columns (wkt to/from wkb conversion)
-- NB: it has to handle 3D geometries (not 4D)
CREATE OR REPLACE FUNCTION update_routes_geom() RETURNS "trigger" AS
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
            -- nb don't touch to route elevation, used for another purpose
        END IF;
        IF NEW.geom_wkt IS NOT NULL AND ndims(NEW.geom_wkt) > 2 THEN -- new data is entered via a 3D WKT -- FIXME : entered data can be 2D for routes !!!!
            NEW.geom:=Transform(GeometryFromText(NEW.geom_wkt, 4326), 900913);  -- storage of 3D 4326 WKT into a 3D 900913 WKB
            NEW.geom_wkt:=AsText(NEW.geom); -- conversion of 3D 900913 WKB into 2D 900913 WKT (loss of information !)
        --ELSEIF NEW.geom IS NOT NULL AND NEW.geom_wkt IS NULL THEN  -- not used ?
        --    NEW.geom_wkt:=AsText(NEW.geom);
        END IF;
        -- warning : at this point, NEW.geom_wkt is always a 2D WKT, because asText does not handle 3D nor 4D. 
        -- Since it is used for representation purposes (OpenLayers), this is no pb.
        IF NEW.geom IS NOT NULL THEN
            geomT = Centroid(Transform(NEW.geom, 4326));
            NEW.lon:=X(geomT); -- of centroid
            NEW.lat:=Y(geomT); -- of centroid
            -- nb don't touch to route elevation, used for another purpose
            -- we automatically update the following fields from the geom generated via GPX or KML upload:
            NEW.route_length := round(ST_length(ST_Transform(NEW.geom,4326),true));
            b = box3D(NEW.geom);
            IF (TG_OP = 'UPDATE') THEN
                IF OLD.min_elevation IS NULL THEN
                    i := round(zmin(b));
                    IF i > 0 THEN
                        NEW.min_elevation := i;
                    END IF;
                END IF;
                IF OLD.max_elevation IS NULL THEN
                    i := round(zmax(b));
                    IF i > 0 THEN
                        NEW.max_elevation := i;
                    END IF;
                END IF;
            END IF;
        END IF;
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom_routes BEFORE INSERT OR UPDATE ON app_routes_archives FOR EACH ROW EXECUTE PROCEDURE update_routes_geom();

CREATE TRIGGER update_search_name_routes_i18n_archives BEFORE INSERT OR UPDATE ON app_routes_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
