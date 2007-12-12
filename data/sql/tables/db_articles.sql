-- Creates the db structure for articles tables ...
--
-- $Id: db_articles.sql 1066 2007-07-26 09:12:07Z alex $ --
--

-- Table app_articles_archives --
CREATE SEQUENCE articles_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_articles_archives (
    article_archive_id integer DEFAULT nextval('articles_archives_seq'::text) NOT NULL,
    categories smallint[],
    activities smallint[],
    article_type smallint
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_articles_archives ADD CONSTRAINT articles_archives_pkey PRIMARY KEY (article_archive_id);
ALTER TABLE app_articles_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 3);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_articles_archives_id_idx ON app_articles_archives USING btree (id); 
CREATE INDEX app_articles_archives_geom_idx ON app_articles_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_articles_archives_redirects_idx ON app_articles_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_articles_archives_latest_idx ON app_articles_archives USING btree (is_latest_version);
CREATE INDEX app_articles_archives_document_archive_id_idx ON app_articles_archives USING btree (document_archive_id);

-- Table app_articles_i18n_archives --
CREATE SEQUENCE articles_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_articles_i18n_archives (
    article_i18n_archive_id integer NOT NULL DEFAULT nextval('articles_i18n_archives_seq'::text),
    abstract text
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_articles_i18n_archives ADD CONSTRAINT articles_i18n_archives_pkey PRIMARY KEY (article_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_articles_i18n_archives_id_culture_idx ON app_articles_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_articles_i18n_archives_name_idx ON app_articles_i18n_archives USING btree (search_name); 
CREATE INDEX app_articles_i18n_archives_latest_idx ON app_articles_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_articles_i18n_archives_document_i18n_archive_id_idx ON app_articles_i18n_archives USING btree (document_i18n_archive_id);

-- Views --
-- elevation of centroid not useful here but is present in view because declared as a property of all document objet in doctrine
CREATE OR REPLACE VIEW articles AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.module, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt, sa.categories, sa.activities, sa.article_type FROM app_articles_archives sa WHERE sa.is_latest_version;

INSERT INTO "geometry_columns" VALUES ('','public','articles','geom',3,900913,'GEOMETRY');

CREATE OR REPLACE VIEW articles_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description, sa.abstract FROM app_articles_i18n_archives sa WHERE sa.is_latest_version;

-- Rules --

CREATE OR REPLACE RULE insert_articles AS ON INSERT TO articles DO INSTEAD
(
    INSERT INTO app_articles_archives (id, module, is_protected, redirects_to, geom_wkt, geom, categories, activities, article_type, is_latest_version) VALUES (NEW.id, 'articles', NEW.is_protected, NEW.redirects_to, NEW.geom_wkt, NEW.geom, NEW.categories, NEW.activities, NEW.article_type, true)
);

CREATE OR REPLACE RULE update_articles AS ON UPDATE TO articles DO INSTEAD
(
    INSERT INTO app_articles_archives (id, module, is_protected, redirects_to, geom_wkt, geom, categories, activities, article_type, is_latest_version) VALUES (NEW.id, 'articles', NEW.is_protected, NEW.redirects_to, NEW.geom_wkt, NEW.geom, NEW.categories, NEW.activities, NEW.article_type, true)
); 

CREATE OR REPLACE RULE insert_articles_i18n AS ON INSERT TO articles_i18n DO INSTEAD
(
    INSERT INTO app_articles_i18n_archives (id, culture, name, search_name, description, abstract, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.abstract, true)
);

CREATE OR REPLACE RULE update_articles_i18n AS ON UPDATE TO articles_i18n DO INSTEAD
(
    INSERT INTO app_articles_i18n_archives (id, culture, name, search_name, description, abstract, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.abstract, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_articles_latest_version BEFORE INSERT ON app_articles_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_articles_archives);
CREATE TRIGGER update_articles_i18n_latest_version BEFORE INSERT ON app_articles_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_articles_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_articles_archives.... --
CREATE TRIGGER insert_articles_i18n_archives AFTER INSERT ON app_articles_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_articles_archives AFTER INSERT ON app_articles_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom_articles BEFORE INSERT OR UPDATE ON app_articles_archives FOR EACH ROW EXECUTE PROCEDURE update_geom(); -- not _pt because the geom associated with an article might be of any type

CREATE TRIGGER update_search_name_articles_i18n_archives BEFORE INSERT OR UPDATE ON app_articles_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
