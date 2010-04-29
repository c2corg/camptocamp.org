-- Creates the db structure for products tables ...
--
-- $Id: db_products.sql 1066 2007-07-26 09:12:07Z alex $ --
--


-- Table app_products_archives --
CREATE SEQUENCE products_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_products_archives (
    product_archive_id integer DEFAULT nextval('products_archives_seq'::text) NOT NULL,
    product_type smallint[],
    url varchar(255)
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_products_archives ADD CONSTRAINT products_archives_pkey PRIMARY KEY (product_archive_id);
ALTER TABLE app_products_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 3);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_products_archives_id_idx ON app_products_archives USING btree (id); 
CREATE INDEX app_products_archives_geom_idx ON app_products_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_products_archives_redirects_idx ON app_products_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_products_archives_latest_idx ON app_products_archives USING btree (is_latest_version);
CREATE INDEX app_products_archives_document_archive_id_idx ON app_products_archives USING btree (document_archive_id);

-- Table app_products_i18n_archives --
CREATE SEQUENCE products_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_products_i18n_archives (
    product_i18n_archive_id integer NOT NULL DEFAULT nextval('products_i18n_archives_seq'::text),
    hours text,
    access text
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_products_i18n_archives ADD CONSTRAINT products_i18n_archives_pkey PRIMARY KEY (product_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_products_i18n_archives_id_culture_idx ON app_products_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_products_i18n_archives_name_idx ON app_products_i18n_archives USING btree (search_name); 
CREATE INDEX app_products_i18n_archives_latest_idx ON app_products_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_products_i18n_archives_document_i18n_archive_id_idx ON app_products_i18n_archives USING btree (document_i18n_archive_id);

-- Views --

CREATE OR REPLACE VIEW products AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.module, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt, sa.product_type, sa.url FROM app_products_archives sa WHERE sa.is_latest_version;
INSERT INTO "geometry_columns" VALUES ('','public','products','geom',3,900913,'POINT');

CREATE OR REPLACE VIEW products_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description, sa.hours, sa.access FROM app_products_i18n_archives sa WHERE sa.is_latest_version;

-- Rules --

CREATE OR REPLACE RULE insert_products AS ON INSERT TO products DO INSTEAD
(
    INSERT INTO app_products_archives (id, module, is_protected, redirects_to, geom, geom_wkt, lon, lat, elevation, product_type, url, is_latest_version) VALUES (NEW.id, 'products', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.lon, NEW.lat, NEW.elevation, NEW.product_type, NEW.url, true)
);

CREATE OR REPLACE RULE update_products AS ON UPDATE TO products DO INSTEAD
(
    INSERT INTO app_products_archives (id, module, is_protected, redirects_to, geom, geom_wkt, lon, lat, elevation, product_type, url, is_latest_version) VALUES (NEW.id, 'products', NEW.is_protected, NEW.redirects_to, NEW.geom, NEW.geom_wkt, NEW.lon, NEW.lat, NEW.elevation, NEW.product_type, NEW.url, true)
); 

CREATE OR REPLACE RULE insert_products_i18n AS ON INSERT TO products_i18n DO INSTEAD
(
    INSERT INTO app_products_i18n_archives (id, culture, name, search_name, description, hours, access, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.hours, NEW.access, true)
);

CREATE OR REPLACE RULE update_products_i18n AS ON UPDATE TO products_i18n DO INSTEAD
(
    INSERT INTO app_products_i18n_archives (id, culture, name, search_name, description, hours, access, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, NEW.hours, NEW.access, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_products_latest_version BEFORE INSERT ON app_products_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_products_archives);
CREATE TRIGGER update_products_i18n_latest_version BEFORE INSERT ON app_products_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_products_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_products_archives.... --
CREATE TRIGGER insert_products_i18n_archives AFTER INSERT ON app_products_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_products_archives AFTER INSERT ON app_products_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom BEFORE INSERT OR UPDATE ON app_products_archives FOR EACH ROW EXECUTE PROCEDURE update_geom_pt();

CREATE TRIGGER update_search_name_products_i18n_archives BEFORE INSERT OR UPDATE ON app_products_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
