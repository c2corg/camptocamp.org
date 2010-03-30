-- Creates the db structure for books tables ...
--
-- $Id: db_books.sql 2267 2007-11-03 18:32:40Z alex $ --
--

-- Table app_books_archives --
CREATE SEQUENCE books_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_books_archives (
    book_archive_id integer DEFAULT nextval('books_archives_seq'::text) NOT NULL,
    author varchar(100),
    editor varchar(100),
    activities smallint[],
    url varchar(255),
    isbn varchar(17),
    book_types smallint[],
    langs char(2)[],
    nb_pages smallint,
    publication_date varchar(100)
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_books_archives ADD CONSTRAINT books_archives_pkey PRIMARY KEY (book_archive_id);
-- a book can be about a path, so that 3D is not stupid:
ALTER TABLE app_books_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 3);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_books_archives_id_idx ON app_books_archives USING btree (id); 
CREATE INDEX app_books_archives_geom_idx ON app_books_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_books_archives_redirects_idx ON app_books_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_books_archives_latest_idx ON app_books_archives USING btree (is_latest_version);
CREATE INDEX app_books_archives_document_archive_id_idx ON app_books_archives USING btree (document_archive_id);

-- Table app_books_i18n_archives --
CREATE SEQUENCE books_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_books_i18n_archives (
    book_i18n_archive_id integer NOT NULL DEFAULT nextval('books_i18n_archives_seq'::text)
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_books_i18n_archives ADD CONSTRAINT books_i18n_archives_pkey PRIMARY KEY (book_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_books_i18n_archives_id_culture_idx ON app_books_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_books_i18n_archives_name_idx ON app_books_i18n_archives USING btree (search_name); 
CREATE INDEX app_books_i18n_archives_latest_idx ON app_books_i18n_archives USING btree (is_latest_version);
CREATE INDEX app_books_i18n_archives_document_i18n_archive_id_idx ON app_books_i18n_archives USING btree (document_i18n_archive_id);

-- Views --

CREATE OR REPLACE VIEW books AS SELECT sa.oid, sa.id, sa.lon, sa.lat, sa.elevation, sa.editor, sa.author, sa.activities, sa.url, sa.isbn, sa.langs, sa.book_types, sa.nb_pages, sa.publication_date, sa.module, sa.is_protected, sa.redirects_to, sa.geom, sa.geom_wkt FROM app_books_archives sa WHERE sa.is_latest_version;

INSERT INTO "geometry_columns" VALUES ('','public','books','geom',3,900913,'GEOMETRY');

CREATE OR REPLACE VIEW books_i18n AS SELECT sa.id, sa.culture, sa.name, sa.search_name, sa.description FROM app_books_i18n_archives sa WHERE sa.is_latest_version;

-- Rules --

CREATE OR REPLACE RULE insert_books AS ON INSERT TO books DO INSTEAD 
(
    INSERT INTO app_books_archives (id, module, is_protected, redirects_to, editor, author, activities, url, isbn, langs, book_types, nb_pages, publication_date, geom_wkt, geom, is_latest_version) VALUES (NEW.id, 'books', NEW.is_protected, NEW.redirects_to, NEW.editor, NEW.author, NEW.activities, NEW.url, NEW.isbn, NEW.langs, NEW.book_types, NEW.nb_pages, NEW.publication_date, NEW.geom_wkt, NEW.geom, true)
);

CREATE OR REPLACE RULE update_books AS ON UPDATE TO books DO INSTEAD 
(
    INSERT INTO app_books_archives (id, module, is_protected, redirects_to, editor, author, activities, url, isbn, langs, book_types, nb_pages, publication_date, geom_wkt, geom, is_latest_version) VALUES (NEW.id, 'books', NEW.is_protected, NEW.redirects_to, NEW.editor, NEW.author, NEW.activities, NEW.url, NEW.isbn, NEW.langs, NEW.book_types, NEW.nb_pages, NEW.publication_date, NEW.geom_wkt, NEW.geom, true)
); 

CREATE OR REPLACE RULE insert_books_i18n AS ON INSERT TO books_i18n DO INSTEAD 
(
    INSERT INTO app_books_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

CREATE OR REPLACE RULE update_books_i18n AS ON UPDATE TO books_i18n DO INSTEAD 
(
    INSERT INTO app_books_i18n_archives (id, culture, name, search_name, description, is_latest_version) VALUES (NEW.id, NEW.culture, NEW.name, NEW.search_name, NEW.description, true)
);

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_books_latest_version BEFORE INSERT ON app_books_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_books_archives);
CREATE TRIGGER update_books_i18n_latest_version BEFORE INSERT ON app_books_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_books_i18n_archives);

-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif d'un sommet dans une langue --
-- executé en premier par Sf, avant le trigger insert_books_archives.... --
CREATE TRIGGER insert_books_i18n_archives AFTER INSERT ON app_books_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les sommets quand on fait une modif "chiffres" sur un sommet --
CREATE TRIGGER insert_books_archives AFTER INSERT ON app_books_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom_books BEFORE INSERT OR UPDATE ON app_books_archives FOR EACH ROW EXECUTE PROCEDURE update_geom();

CREATE TRIGGER update_search_name_books_i18n_archives BEFORE INSERT OR UPDATE ON app_books_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();
