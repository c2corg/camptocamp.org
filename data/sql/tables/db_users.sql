-- Creates the user tables, views, rules, triggers ...
--
-- $Id: db_users.sql 2247 2007-11-02 13:56:21Z alex $ --
--

--
CREATE SEQUENCE app_users_archives_id_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_users_archives (
    user_archive_id integer NOT NULL DEFAULT nextval('app_users_archives_id_seq'::text),
    activities smallint[],
    category smallint,
    v4_id smallint
) INHERITS (app_documents_archives);

ALTER TABLE app_users_archives ADD CONSTRAINT app_users_archives_pkey PRIMARY KEY (user_archive_id);
ALTER TABLE app_users_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 2);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_users_archives_id_idx ON app_users_archives USING btree (id); 
CREATE INDEX app_users_archives_geom_idx ON app_users_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_users_archives_redirects_idx ON app_users_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_users_archives_v4_idx ON app_users_archives USING btree (v4_id);
CREATE INDEX app_users_archives_latest_idx ON app_users_archives USING btree (is_latest_version);
CREATE INDEX app_users_archives_document_archive_id_idx ON app_users_archives USING btree (document_archive_id);

-- Some data do not require versioning
CREATE TABLE app_users_private_data (
    password_tmp character varying(255),
    topo_name character varying(200),              -- this name is used for the guidebook
    login_name character varying(200),             -- this is the symfony username ! username field is now used as a nickname !!! because of punbb...
    search_username character varying(200),         -- for searching on username (forum name)
    document_culture character varying(20) NOT NULL,
    is_profile_public boolean NOT NULL DEFAULT false,
    v4_id smallint,
    pref_cookies text
) INHERITS (punbb_users);
-- there exists an implicit index on 'id', due to the fact that it is a PK.
-- but it is not inherited on this daughter table, thus:
CREATE INDEX app_users_private_data_id_idx ON app_users_private_data USING btree (id); 
CREATE INDEX app_users_private_data_search_username_idx ON app_users_private_data USING btree (search_username);
-- FIXME: more indexes on this table (name..., (id, document_culture) ? ) ?


-- Table users_i18n_archives --
CREATE SEQUENCE users_i18n_archives_id_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_users_i18n_archives (
    user_i18n_archive_id integer NOT NULL DEFAULT nextval('users_i18n_archives_id_seq'::text)
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_users_i18n_archives ADD CONSTRAINT users_i18n_archives_pkey PRIMARY KEY (user_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_users_i18n_archives_id_culture_idx ON app_users_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_users_i18n_archives_name_idx ON app_users_i18n_archives USING btree (search_name); 
CREATE INDEX app_users_i18n_archives_latest_idx ON app_users_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_users_i18n_archives_document_i18n_archive_id_idx ON app_users_i18n_archives USING btree (document_i18n_archive_id);

-- Views --
-- elevation appears here, because it is defined in doctrine model for every document. null for users.
CREATE OR REPLACE VIEW users AS SELECT ua.oid, ua.id, ua.module, ua.is_protected, ua.redirects_to, ua.lon, ua.lat, ua.elevation, ua.geom, ua.geom_wkt, ua.v4_id, ua.activities, ua.category FROM app_users_archives ua WHERE ua.is_latest_version;
INSERT INTO "geometry_columns" VALUES ('','public','users','geom',2,900913,'POINT');

CREATE OR REPLACE VIEW users_i18n AS SELECT ua.id, ua.culture, ua.name, ua.search_name, ua.description FROM app_users_i18n_archives ua WHERE ua.is_latest_version;

-- Rules --
-- no rule to update/insert elevation.
CREATE RULE insert_users AS ON INSERT TO users DO INSTEAD 
(
    INSERT INTO app_users_archives (id, module, is_protected, redirects_to, lon, lat, geom_wkt, geom, v4_id, activities, category, is_latest_version) VALUES (NEW.id, 'users', NEW.is_protected, NEW.redirects_to, NEW.lon, NEW.lat, NEW.geom_wkt, NEW.geom, NEW.v4_id, NEW.activities, NEW.category, true)
);

CREATE RULE update_users AS ON UPDATE TO users DO INSTEAD 
(
    INSERT INTO app_users_archives (id, module, is_protected, redirects_to, lon, lat, geom_wkt, geom, v4_id, activities, category, is_latest_version) VALUES (NEW.id, 'users', NEW.is_protected, NEW.redirects_to, NEW.lon, NEW.lat, NEW.geom_wkt, NEW.geom, NEW.v4_id, NEW.activities, NEW.category, true)
);

CREATE RULE delete_users AS ON DELETE TO users DO INSTEAD 
(
    DELETE FROM app_users_archives
        WHERE id = OLD.id;
);

CREATE RULE insert_users_i18n AS ON INSERT TO users_i18n DO INSTEAD 
(
    INSERT INTO app_users_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

CREATE RULE update_users_i18n AS ON UPDATE TO users_i18n DO INSTEAD 
(
    INSERT INTO app_users_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

CREATE RULE delete_users_i18n AS ON DELETE TO users_i18n DO INSTEAD 
(
    DELETE FROM app_users_i18n_archives
        WHERE id = OLD.id;
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_users_latest_version BEFORE INSERT ON app_users_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_users_archives);
CREATE TRIGGER update_users_i18n_latest_version BEFORE INSERT ON app_users_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_users_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_app_users_archives.... --
CREATE TRIGGER insert_users_i18n_archives AFTER INSERT ON app_users_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_users_archives AFTER INSERT ON app_users_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- Trigger pour déclencher la copie de topo_name dans le nom du document correspondant lorsque celui est modifié --
CREATE TRIGGER update_topo_name AFTER UPDATE ON app_users_private_data FOR EACH ROW EXECUTE PROCEDURE update_topo_name();

-- Trigger pour mettre à jour search_username lorsque username est modifié --
CREATE TRIGGER update_search_username BEFORE INSERT OR UPDATE ON app_users_private_data FOR EACH ROW EXECUTE PROCEDURE update_search_username();

-- function that updates the geom point columns (wkt to/from wkb conversion)
-- used for 2D POINT documents : users
CREATE OR REPLACE FUNCTION update_2dgeom_pt() RETURNS "trigger" AS
$BODY$
    DECLARE
        geomT geometry;
    BEGIN
        IF NEW.geom_wkt IS NULL AND NEW.geom IS NOT NULL THEN -- this is the case when we want to delete a document's geometry
            NEW.geom:=null;
            NEW.lon:=null; -- of centroid
            NEW.lat:=null; -- of centroid
            NEW.elevation:=null; -- of centroid
        END IF;
        IF NEW.lon IS NOT NULL AND NEW.lat IS NOT NULL THEN
            NEW.geom:=Transform(GeomFromEWKT('SRID=4326;POINT( ' || NEW.lon || ' ' || NEW.lat || ')'), 900913); 
            NEW.geom_wkt:=AsText(NEW.geom);
    	ELSEIF NEW.geom_wkt IS NOT NULL THEN
            NEW.geom:=GeometryFromText(NEW.geom_wkt, 900913);
            geomT = Transform(NEW.geom, 4326);
            NEW.lon:=X(geomT);
            NEW.lat:=Y(geomT);
        ELSEIF NEW.geom IS NOT NULL THEN
            NEW.geom_wkt:=AsText(NEW.geom);
            geomT = Transform(NEW.geom, 4326);
            NEW.lon:=X(geomT);
            NEW.lat:=Y(geomT);
        END IF;
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom_users BEFORE INSERT OR UPDATE ON app_users_archives FOR EACH ROW EXECUTE PROCEDURE update_2dgeom_pt();

CREATE TRIGGER update_search_name_users_i18n_archives BEFORE INSERT OR UPDATE ON app_users_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
