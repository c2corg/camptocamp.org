-- Creates the db structure for portals tables ...


-- Table app_portals_archives --
CREATE SEQUENCE portals_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_portals_archives (
    portal_archive_id integer DEFAULT nextval('portals_archives_seq'::text) NOT NULL,
    activities smallint[],
    has_map boolean,
    map_filter varchar(255),
    topo_filter varchar(255),
    nb_outings smallint,
    outing_filter varchar(255),
    nb_images smallint,
    image_filter varchar(255),
    nb_videos smallint,
    video_filter varchar(255),
    nb_articles smallint,
    article_filter varchar(255),
    nb_topics smallint,
    forum_filter varchar(255),
    nb_news smallint,
    news_filter varchar(255),
    design_file varchar(255)
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_portals_archives ADD CONSTRAINT portals_archives_pkey PRIMARY KEY (portal_archive_id);
ALTER TABLE app_portals_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 3);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_portals_archives_id_idx ON app_portals_archives USING btree (id); 
CREATE INDEX app_portals_archives_geom_idx ON app_portals_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_portals_archives_redirects_idx ON app_portals_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_portals_archives_latest_idx ON app_portals_archives USING btree (is_latest_version);
CREATE INDEX app_portals_archives_document_archive_id_idx ON app_portals_archives USING btree (document_archive_id);

-- Table app_portals_i18n_archives --
CREATE SEQUENCE portals_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_portals_i18n_archives (
    portal_i18n_archive_id integer NOT NULL DEFAULT nextval('portals_i18n_archives_seq'::text),
    abstract text
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_portals_i18n_archives ADD CONSTRAINT portals_i18n_archives_pkey PRIMARY KEY (portal_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_portals_i18n_archives_id_culture_idx ON app_portals_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_portals_i18n_archives_name_idx ON app_portals_i18n_archives USING btree (search_name); 
CREATE INDEX app_portals_i18n_archives_latest_idx ON app_portals_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_portals_i18n_archives_document_i18n_archive_id_idx ON app_portals_i18n_archives USING btree (document_i18n_archive_id);

-- Views --

CREATE OR REPLACE VIEW portals AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.module, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt, sa.activities, sa.has_map, sa.map_filter, sa.topo_filter, sa.nb_outings, sa.outing_filter, sa.nb_images, sa.image_filter, sa.nb_videos, sa.video_filter, sa.nb_articles, sa.article_filter, sa.nb_topics, sa.forum_filter, sa.nb_news, sa.news_filter, sa.design_file FROM app_portals_archives sa WHERE sa.is_latest_version;
INSERT INTO "geometry_columns" VALUES ('','public','portals','geom',3,900913,'POINT');

CREATE OR REPLACE VIEW portals_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description, sa.abstract FROM app_portals_i18n_archives sa WHERE sa.is_latest_version;

-- Rules --

CREATE OR REPLACE RULE insert_portals AS ON INSERT TO portals DO INSTEAD
(
    INSERT INTO app_portals_archives (id, module, is_protected, redirects_to, geom, geom_wkt, lon, lat, elevation, activities, has_map, map_filter, topo_filter, nb_outings, outing_filter, nb_images, image_filter, nb_videos, video_filter, nb_articles, article_filter, nb_topics, forum_filter, nb_news, news_filter, design_file, is_latest_version) VALUES (NEW.id, 'portals', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.lon, NEW.lat, NEW.elevation, NEW.activities, NEW.has_map, NEW.map_filter, NEW.topo_filter, NEW.nb_outings, NEW.outing_filter, NEW.nb_images, NEW.image_filter, NEW.nb_videos, NEW.video_filter, NEW.nb_articles, NEW.article_filter, NEW.nb_topics, NEW.forum_filter, NEW.nb_news, NEW.news_filter, NEW.design_file, true)
);

CREATE OR REPLACE RULE update_portals AS ON UPDATE TO portals DO INSTEAD
(
    INSERT INTO app_portals_archives (id, module, is_protected, redirects_to, geom, geom_wkt, lon, lat, elevation, activities, has_map, map_filter, topo_filter, nb_outings, outing_filter, nb_images, image_filter, nb_videos, video_filter, nb_articles, article_filter, nb_topics, forum_filter, nb_news, news_filter, design_file, is_latest_version) VALUES (NEW.id, 'portals', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.lon, NEW.lat, NEW.elevation, NEW.activities, NEW.has_map, NEW.map_filter, NEW.topo_filter, NEW.nb_outings, NEW.outing_filter, NEW.nb_images, NEW.image_filter, NEW.nb_videos, NEW.video_filter, NEW.nb_articles, NEW.article_filter, NEW.nb_topics, NEW.forum_filter, NEW.nb_news, NEW.news_filter, NEW.design_file, true)
); 

CREATE OR REPLACE RULE insert_portals_i18n AS ON INSERT TO portals_i18n DO INSTEAD
(
    INSERT INTO app_portals_i18n_archives (id, culture, name, search_name, description, abstract, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.abstract, true)
);

CREATE OR REPLACE RULE update_portals_i18n AS ON UPDATE TO portals_i18n DO INSTEAD
(
    INSERT INTO app_portals_i18n_archives (id, culture, name, search_name, description, abstract, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.abstract, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_portals_latest_version BEFORE INSERT ON app_portals_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_portals_archives);
CREATE TRIGGER update_portals_i18n_latest_version BEFORE INSERT ON app_portals_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_portals_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_portals_archives.... --
CREATE TRIGGER insert_portals_i18n_archives AFTER INSERT ON app_portals_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_portals_archives AFTER INSERT ON app_portals_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom BEFORE INSERT OR UPDATE ON app_portals_archives FOR EACH ROW EXECUTE PROCEDURE update_geom_pt();

CREATE TRIGGER update_search_name_portals_i18n_archives BEFORE INSERT OR UPDATE ON app_portals_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
