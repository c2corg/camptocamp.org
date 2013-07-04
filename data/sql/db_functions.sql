-- Creates the basic general functions needed for the site to work ...
-- $Id: db_functions.sql 2455 2007-11-30 11:44:31Z alex $
-- 

-- function that returns the revision number of a document, given its culture and document_id
-- what it returns is based on the contents of the app_documents_versions table
CREATE OR REPLACE FUNCTION latest_version(document_id integer, culture char(2)) RETURNS integer AS 
$BODY$
    DECLARE
        rev integer;
    BEGIN
	SELECT INTO rev dv.version FROM app_documents_versions dv WHERE dv.document_id=latest_version.document_id AND dv.culture=latest_version.culture AND dv.documents_versions_id IN (SELECT MAX(dv.documents_versions_id) FROM app_documents_versions dv GROUP BY dv.document_id, dv.culture);
	IF rev IS NULL THEN
	    RETURN 0; -- 0 validated --
	ELSE
	    RETURN rev;
	END IF;
    END;
$BODY$ 
LANGUAGE plpgsql;



-- function that returns the document_archive_id corresponding to the last revision of the "figures part of the document" whose id is passed as an argument. 
CREATE OR REPLACE FUNCTION latest_document_archive_id(document_id integer) RETURNS integer AS 
$BODY$
    DECLARE
        doc_id integer;
    BEGIN
		SELECT INTO doc_id MAX(app_documents_archives.document_archive_id) FROM app_documents_archives WHERE app_documents_archives.id = latest_document_archive_id.document_id;
	IF doc_id IS NULL THEN 
	    RETURN 1; 
	ELSE
	    RETURN doc_id;
	END IF;
    END;
$BODY$ 
LANGUAGE plpgsql;


-- function that returns a boolean specifying whether the document whose id is passed as argument exists
CREATE OR REPLACE FUNCTION document_exists(document_id integer) RETURNS boolean AS 
$BODY$
    BEGIN
	   RETURN EXISTS(SELECT 1 FROM documents WHERE documents.id=document_exists.document_id);
    END;
$BODY$
LANGUAGE plpgsql;


-- function that returns a boolean specifying whether the document whose id is passed as argument exists in the specified langage
CREATE OR REPLACE FUNCTION document_exists_in(lang char(2), document_id integer) RETURNS boolean AS 
$BODY$
    BEGIN
	   RETURN EXISTS(SELECT 1 FROM documents_i18n WHERE documents_i18n.id=document_exists_in.document_id AND documents_i18n.culture=document_exists_in.lang);
    END;
$BODY$
LANGUAGE plpgsql;



-- function that returns a boolean specifying whether the revision of doc_id made at the timestamp specified is a text+figures one.
-- warning : it only looks inside the N last records of app_documents_versions (where N is the number of available cultures)
CREATE OR REPLACE FUNCTION exists_in_documents_versions(doc_id integer, lang char(2), timestp timestamp without time zone) RETURNS boolean AS 
$BODY$
    BEGIN
	    RETURN EXISTS(SELECT 1 FROM (SELECT * FROM latest_documents_versions_records) AS latest WHERE latest.document_id=exists_in_documents_versions.doc_id AND latest.culture=exists_in_documents_versions.lang AND latest.created_at=exists_in_documents_versions.timestp);
    END;
$BODY$
LANGUAGE plpgsql;


-- function that returns the document_i18n_archive_id corresponding to the last "text" revision of document_id in "lang" culture
-- warning : one always has to launch it when one knows that this document exists or will exist very soon (insertion pending) in this culture, or else, it might return a wrong result (1)
CREATE OR REPLACE FUNCTION latest_document_i18n_archive_id(document_id integer, lang char(2)) RETURNS integer AS 
$BODY$
    DECLARE
        doc_i18n_id integer;
    BEGIN
        SELECT INTO doc_i18n_id document_i18n_archive_id FROM app_documents_i18n_archives WHERE id = latest_document_i18n_archive_id.document_id AND culture = latest_document_i18n_archive_id.lang AND document_i18n_archive_id IN (SELECT MAX(document_i18n_archive_id) FROM app_documents_i18n_archives GROUP BY id, culture);
	IF doc_i18n_id IS NULL THEN
	    RETURN 1;
	ELSE
	    RETURN doc_i18n_id;
	END IF;
    END;
$BODY$ 
LANGUAGE plpgsql;




-- function that returns the history_metadata_id corresponding to the current metadata revision of document_id in "lang" culture at T time 
CREATE OR REPLACE FUNCTION current_history_metadata_id(t timestamp) RETURNS integer AS 
$BODY$
    DECLARE
        hist_md_id integer;
    BEGIN
        SELECT INTO hist_md_id history_metadata_id FROM app_history_metadata WHERE written_at = current_history_metadata_id.t;
	IF hist_md_id IS NULL THEN
	    RETURN 1; 
	ELSE
	    RETURN hist_md_id;
	END IF;
    END;
$BODY$ 
LANGUAGE plpgsql;



-- function that updates the app_documents_versions history table (launched when one updates a "figures" archive table, daughter of documents_archives).
-- actually, it increments the document's revision number in every supported culture for which this document exists.   

--> function last triggered in case of (UPDATE) on a summit object for instance (classical case).  
--> but executed first in case of summit creation (INSERT) : we then have to insert some sparse data into app_documents_versions to notify update_documents_versions_i18n
  
CREATE OR REPLACE FUNCTION update_documents_versions() RETURNS "trigger" AS
$BODY$
    DECLARE
        lang record;
        currenttime timestamp without time zone;
        hm_id integer; 
        test boolean;
    BEGIN
        test = true;
        currenttime = NOW();
        hm_id = (SELECT current_history_metadata_id(currenttime));
        IF document_exists(NEW.id) THEN
            FOR lang IN SELECT * FROM app_cultures LOOP
                IF document_exists_in(lang.culture, NEW.id) THEN
                    IF exists_in_documents_versions(NEW.id, lang.culture, currenttime) THEN
                        -- the reference to the i18n record has already been inserted in documents_versions ...
                        -- and we are doing a "Figures + Text" revision of the document ("ft")
                        -- so we have to update its reference to document_archive_id, and set the nature of the revision
                        UPDATE app_documents_versions SET document_archive_id = NEW.document_archive_id, nature = 'ft' WHERE document_id = NEW.id AND culture = lang.culture AND created_at = currenttime;
                        test = false;
                    ELSE
                        -- we are just doing a "Figures" revision of the current document (nature = "fo" stands for "figures only")
                        INSERT INTO app_documents_versions(document_id, culture, document_i18n_archive_id, created_at, version, document_archive_id, nature, history_metadata_id) 
                        VALUES (NEW.id, lang.culture, (SELECT latest_document_i18n_archive_id(NEW.id, lang.culture)), currenttime, (SELECT latest_version(NEW.id, lang.culture)+1), NEW.document_archive_id, 'fo', hm_id);
                        test = false; 
                    END IF;
                END IF;
            END LOOP;
            IF (test) THEN --> in this case (summit creation for instance), insert into summits first, then insert into summits_i18n 
                INSERT INTO app_documents_versions(document_id, culture, document_i18n_archive_id, created_at, version, document_archive_id, nature, history_metadata_id) 
                VALUES (NEW.id, 'en', 0, currenttime, 1, NEW.document_archive_id, 'ft', hm_id);
                -- culture 'en' (fake) + document_i18n_archive_id to be set lately by update_documents_versions_i18n().
            END IF;
        ELSE --> from document_exists(NEW.id)  
            -- in this case, document has just been created in a given language => nature='ft'
            -- one still does not know the value of document_i18n_archive_id, nor its culture.
            -- but one knows that this is version 1 on the document in this language.
            INSERT INTO app_documents_versions(document_id, culture, document_i18n_archive_id, created_at, version, document_archive_id, nature, history_metadata_id) 
            VALUES (NEW.id, 'en', 0, currenttime, 1, NEW.document_archive_id, 'ft', hm_id);  
            -- culture 'en' (fake) + document_i18n_archive_id to be set lately by update_documents_versions_i18n().
        END IF;
       RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;


-- function that updates the app_documents_versions history table (launched when one updates a "i18n" archive table, daughter of documents_i18n_archives).
-- actually, it increments the document's revision number in this culture. 

--> function first triggered when one does an (UPDATE). Then, we have to insert new data into app_documents_versions 
--> but it is last executed when one creates a summit (INSERT). Then, we have to update the previous record in app_documents_versions that has just been created by update_documents_versions().

CREATE OR REPLACE FUNCTION update_documents_versions_i18n() RETURNS "trigger" AS
$BODY$
    DECLARE
        currenttime timestamp without time zone; 
    BEGIN
        currenttime = NOW();
        IF exists_in_documents_versions(NEW.id, 'en', currenttime) THEN
            UPDATE app_documents_versions SET culture = NEW.culture, document_i18n_archive_id = NEW.document_i18n_archive_id 
            WHERE document_id = NEW.id AND culture = 'en' AND created_at = currenttime; -- nature = 'ft' has already been set in this case.
        ELSE
            INSERT INTO app_documents_versions(document_id, culture, document_i18n_archive_id, created_at, version, document_archive_id, nature, history_metadata_id) 
            VALUES (NEW.id, NEW.culture, NEW.document_i18n_archive_id, currenttime, (SELECT latest_version(NEW.id, NEW.culture)+1), (SELECT latest_document_archive_id(NEW.id)), 'to', (SELECT current_history_metadata_id(currenttime)) );
            -- "to" means "text only"
        END IF;
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;

--
-- GEOMETRY
--

-- function that updates the geom point columns (wkt to/from wkb conversion)
-- used for 3D POINT documents : summits huts images parkings (3D geom : X, Y, Z), 
CREATE OR REPLACE FUNCTION update_geom_pt() RETURNS "trigger" AS
$BODY$
    DECLARE
        altitude smallint;
    BEGIN
        IF NEW.geom_wkt IS NULL AND NEW.geom IS NOT NULL THEN -- this is the case when we want to delete a document's geometry
            NEW.geom:=null;
            NEW.lon:=null; -- of centroid
            NEW.lat:=null; -- of centroid
            NEW.elevation:=null; -- of centroid
        END IF;
        IF NEW.lon IS NOT NULL AND NEW.lat IS NOT NULL THEN
            IF NEW.elevation IS NOT NULL THEN
                altitude = NEW.elevation;
            ELSE
                altitude = 0;
            END IF;
            NEW.geom:=Transform(GeomFromEWKT('SRID=4326;POINT( ' || NEW.lon || ' ' || NEW.lat || ' ' || altitude || ')'), 900913); 
            NEW.geom_wkt:=AsText(NEW.geom);-- warning : this is a 2D WKT, because asText does not handle 3D nor 4D 
            -- as it is used for representation purposes (OpenLayers), this is no pb.
        ELSE
            NEW.geom:=null;
            NEW.geom_wkt:=null;
        END IF;
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;


-- function that updates the geom columns (wkt to/from wkb conversion)
-- NB: it now has to handle 3D and 4D geometries, but this should not be a problem.
-- it is used for undefined 3D geometry types (books, articles) 
CREATE OR REPLACE FUNCTION update_geom() RETURNS "trigger" AS
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
        IF NEW.geom_wkt IS NOT NULL THEN
            NEW.geom:=Transform(GeometryFromText(NEW.geom_wkt, 4326), 900913);
            NEW.geom_wkt:=AsText(NEW.geom); -- warning : this is a 2D WKT, because asText does not handle 3D nor 4D 
            -- as it is used for representation purposes (OpenLayers), this is no pb.
            geomT = Centroid(Transform(NEW.geom, 4326));
            NEW.lon:=X(geomT);
            NEW.lat:=Y(geomT);
            NEW.elevation:=Z(geomT);
        ELSEIF NEW.geom IS NOT NULL THEN
            NEW.geom_wkt:=AsText(NEW.geom);
            geomT = Centroid(Transform(NEW.geom, 4326));
            NEW.lon:=X(geomT);
            NEW.lat:=Y(geomT);
            NEW.elevation:=Z(geomT);
        END IF;
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;

-- Next 2 functions set "is_latest_version" flag to false before adding a new version.
-- Name of archive table to update must be passed as argument.
CREATE OR REPLACE FUNCTION reset_latest_version() RETURNS "trigger" AS
$BODY$
    BEGIN
        EXECUTE 'UPDATE ' || TG_ARGV[0] || ' SET is_latest_version = FALSE WHERE id = ' || NEW.id || ' AND is_latest_version';
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION reset_latest_version_i18n() RETURNS "trigger" AS
$BODY$
    BEGIN
        EXECUTE 'UPDATE ' || TG_ARGV[0] || ' SET is_latest_version = FALSE WHERE id = ' || NEW.id || ' AND is_latest_version AND culture = ''' || NEW.culture || '''';
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;

-- Copy the 'topo_name' to 'name' of app_users_i18n_archives
CREATE OR REPLACE FUNCTION update_topo_name() RETURNS "trigger" AS
$BODY$
    BEGIN
        IF OLD.topo_name != NEW.topo_name THEN
            UPDATE app_users_i18n_archives SET name = NEW.topo_name WHERE id = NEW.id;
        END IF;
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;

-- Replaces accentuated characters by their non-accentuated equivalents and lower-case result
CREATE OR REPLACE FUNCTION remove_accents(string text) RETURNS text AS
$BODY$
    BEGIN
        RETURN lower(translate(string,
                               'ÀÁÂÃÄÅàáâãäåÇČçčÈÉÊËèéêëÌÍÎÏìíîïÑñÒÓÔÕÖØòóôõöøŠšÙÚÛÜùúûüÝΫýÿŽžßœ-():',
                               'AAAAAAaaaaaaCCccEEEEeeeeIIIIiiiiNnOOOOOOooooooSsUUUUuuuuYYyyZzso    '));
        
        -- following solution is cool but crashes on some special UTF8 characters with no equivalents in LATIN9  
        --RETURN lower(to_ascii(convert(string, 'UTF8', 'LATIN9'), 'LATIN9'));
    END;
$BODY$
LANGUAGE plpgsql IMMUTABLE;

-- Makes some further string substitutions
CREATE OR REPLACE FUNCTION make_substitutions(string text) RETURNS text AS
$BODY$
    DECLARE
        tmp text;
        chs text;
        ch char;
    BEGIN
        -- ß->ss oe->o ue->u ae->a (german)
        -- saint->st
        tmp = replace(string, 'oe', 'o');
        tmp = replace(tmp, 'ae', 'a');
        tmp = replace(tmp, 'ue', 'u');
        tmp = replace(tmp, 'saint', 'st');

        -- remove doubled letters
        chs = 'abcdefghijklmnopqrstuvwxyz';
        FOR i IN 1..length(chs) LOOP
            ch = substr(chs, i, 1);
            tmp = replace(tmp, ch || ch, ch);
        END LOOP;
        RETURN tmp;
    END;
$BODY$
LANGUAGE plpgsql IMMUTABLE;

-- Remove keywords that should not be taken into account
CREATE OR REPLACE FUNCTION remove_keywords(string text) RETURNS text AS
$BODY$
    DECLARE
        i integer;
        word text;
        all_words text[];
        final_words text[];
        excluded_words text[];
        escaped_string text;
    BEGIN
        excluded_words = ARRAY['','le','la','les','de','des','du','a','au','aux','the','da','das','der','die','des', 'en', E'd''', E'l'''];
        
        -- add a space to quotes and then explode string into an array using whitespaces
        
        -- works with pg 8.3 only
        --all_words = regexp_split_to_array(
	    --    translate (
		--        replace(btrim(string), E'''', E''' '),
	    --        '-():', '    '
	    --    ),
	    --    E'\\s+'
        --);
        
        -- works with pg 8.2
        escaped_string = translate(
            replace(btrim(string), E'''', E''' '),
            '-():', '    '
        );
        all_words = string_to_array(escaped_string, ' ');      
        IF array_upper(all_words, 1) IS NULL THEN
            RETURN escaped_string;
        END IF;

        FOR i IN 1..array_upper(all_words, 1) LOOP
            word =  all_words[i];
            -- check if word is contained in excluded_words
            IF NOT string_to_array(word, '')  <@ excluded_words THEN 
                final_words = array_append(final_words, word);
            END IF;
        END LOOP;
        
        RETURN array_to_string(final_words, ' ');
	END;
$BODY$
LANGUAGE plpgsql IMMUTABLE;

CREATE OR REPLACE FUNCTION make_search_name(string text) RETURNS text AS
$BODY$
    BEGIN
        RETURN make_substitutions(remove_accents(remove_keywords(string)));
    END;
$BODY$
LANGUAGE plpgsql IMMUTABLE;


CREATE OR REPLACE FUNCTION update_search_name() RETURNS "trigger" AS
$BODY$
    BEGIN
        NEW.search_name := make_search_name(NEW.name);
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION update_search_username() RETURNS "trigger" AS
$BODY$
    BEGIN
        NEW.search_username := make_search_name(NEW.username);
        RETURN NEW;
    END;
$BODY$
LANGUAGE plpgsql;

-- Punbb function to handle users online status list
CREATE OR REPLACE FUNCTION punbb_update_users_online(max_online integer, max_visit integer) RETURNS integer AS
$BODY$
    DECLARE
        user RECORD;
    BEGIN
        FOR user IN SELECT logged, user_id, idle FROM punbb_online WHERE logged < max_online LOOP
            IF logged < punbb_update_users_online.max_visit THEN
                EXECUTE 'UPDATE punbb_users SET last_visit =' || logged || ', read_topics = NULL WHERE id = ' || user_id;
                EXECUTE 'DELETE FROM punbb_online WHERE user_id = ' || user_id;
            ELSEIF idle = 0 THEN
                EXECUTE 'UPDATE punbb_online SET idle = 1 WHERE user_id = ' || user_id;
            END IF;
        END LOOP;
        RETURN 1;
    END
$BODY$
LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION makePointWkt(lon numeric, lat numeric) RETURNS text AS
$BODY$
    BEGIN
        RETURN AsText(Transform(SetSrid(MakePoint(lon, lat), 4326), 900913));
    END;
$BODY$
LANGUAGE plpgsql;
