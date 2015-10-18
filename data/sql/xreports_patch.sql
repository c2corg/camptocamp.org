BEGIN;

-- Creates the db structure for xreports tables ...
--
-- xreports_patch.sql
--

-------------------------------------------------------------------------------
-- Table app_xreports_archives --
CREATE SEQUENCE xreports_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_xreports_archives (
    xreport_archive_id integer DEFAULT nextval('xreports_archives_seq'::text) NOT NULL,
    date date,
    activities smallint[],
    nb_participants smallint,
    nb_impacted smallint,
    severity smallint,
    rescue boolean,
    event_type smallint[],
    avalanche_level smallint,
    avalanche_slope smallint,
    author_status smallint,
    activity_rate smallint,
    nb_outings smallint,
    autonomy smallint,
    age smallint,
    gender smallint,
    previous_injuries smallint
) INHERITS (app_documents_archives);

ALTER TABLE ONLY app_xreports_archives ADD CONSTRAINT xreports_archives_pkey PRIMARY KEY (xreport_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_archives):
CREATE INDEX app_xreports_archives_id_idx ON app_xreports_archives USING btree (id); 
CREATE INDEX app_xreports_archives_geom_idx ON app_xreports_archives USING GIST (geom GIST_GEOMETRY_OPS); 
CREATE INDEX app_xreports_archives_redirects_idx ON app_xreports_archives USING btree (redirects_to); -- useful for filtering on lists
CREATE INDEX app_xreports_archives_elevation_idx ON app_xreports_archives USING btree (elevation);
ALTER TABLE app_xreports_archives ADD CONSTRAINT enforce_dims_geom CHECK (ndims(geom) = 3);

CREATE INDEX app_xreports_archives_latest_idx ON app_xreports_archives USING btree (is_latest_version);
CREATE INDEX app_xreports_archives_document_archive_id_idx ON app_xreports_archives USING btree (document_archive_id);

-------------------------------------------------------------------------------
-- Table app_xreports_i18n_archives --
CREATE SEQUENCE xreports_i18n_archives_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

CREATE TABLE app_xreports_i18n_archives (
    xreport_i18n_archive_id integer NOT NULL DEFAULT nextval('xreports_i18n_archives_seq'::text),
    place text,
    route_study text,
    conditions text,
    training text,
    motivations text,
    group_management text,
    risk text,
    time_management text,
    safety text,
    reduce_impact text,
    increase_impact text,
    modifications text,
    other_comments text
) INHERITS (app_documents_i18n_archives);

ALTER TABLE ONLY app_xreports_i18n_archives ADD CONSTRAINT xreports_i18n_archives_pkey PRIMARY KEY (xreport_i18n_archive_id);
-- added here because indexes are not inherited from parent table (app_documents_i18n_archives):
CREATE INDEX app_xreports_i18n_archives_id_culture_idx ON app_xreports_i18n_archives USING btree (id, culture);
-- index for text search:
-- nb : be sure to search on the lowercased version of word typed, so that this index is useful.
CREATE INDEX app_xreports_i18n_archives_name_idx ON app_xreports_i18n_archives USING btree (search_name); 
CREATE INDEX app_xreports_i18n_archives_latest_idx ON app_xreports_i18n_archives USING btree (is_latest_version); 
CREATE INDEX app_xreports_i18n_archives_document_i18n_archive_id_idx ON app_xreports_i18n_archives USING btree (document_i18n_archive_id);

-------------------------------------------------------------------------------
-- Views --
CREATE OR REPLACE VIEW xreports AS SELECT
    sa.oid,
    sa.id,
    sa.lon,
    sa.lat,
    sa.elevation,
    sa.date,
    sa.activities,
    sa.nb_participants,
    sa.nb_impacted,
    sa.severity,
    sa.rescue,
    sa.event_type,
    sa.avalanche_level,
    sa.avalanche_slope,
    sa.author_status,
    sa.activity_rate,
    sa.nb_outings,
    sa.autonomy,
    sa.age,
    sa.gender,
    sa.previous_injuries,
    sa.module,
    sa.is_protected,
    sa.redirects_to,
    sa.geom,
    sa.geom_wkt
FROM app_xreports_archives sa WHERE sa.is_latest_version; 
INSERT INTO "geometry_columns" VALUES ('','public','xreports','geom',3,900913,'POINT');

CREATE OR REPLACE VIEW xreports_i18n AS SELECT
    sa.id,
    sa.culture,
    sa.name,
    sa.search_name,
    sa.description,
    sa.place,
    sa.route_study,
    sa.conditions,
    sa.training,
    sa.motivations,
    sa.group_management,
    sa.risk,
    sa.time_management,
    sa.safety,
    sa.reduce_impact,
    sa.increase_impact,
    sa.modifications,
    sa.other_comments
FROM app_xreports_i18n_archives sa WHERE sa.is_latest_version;


-------------------------------------------------------------------------------
-- Rules --
CREATE OR REPLACE RULE insert_xreports AS ON INSERT TO xreports DO INSTEAD 
(
    INSERT INTO app_xreports_archives
    (
        id,
        module,
        is_protected,
        redirects_to,
        lon,
        lat,
        geom_wkt,
        geom,
        elevation,
        date,
        activities,
        nb_participants,
        nb_impacted,
        severity,
        rescue,
        event_type,
        avalanche_level,
        avalanche_slope,
        author_status,
        activity_rate,
        nb_outings,
        autonomy,
        age,
        gender,
        previous_injuries,
        is_latest_version
    )
    VALUES
    (
        NEW.id,
        'xreports',
        NEW.is_protected,
        NEW.redirects_to,
        NEW.lon,
        NEW.lat,
        NEW.geom_wkt,
        NEW.geom,
        NEW.elevation,
        NEW.date,
        NEW.activities,
        NEW.nb_participants,
        NEW.nb_impacted,
        NEW.severity,
        NEW.rescue,
        NEW.event_type,
        NEW.avalanche_level,
        NEW.avalanche_slope,
        NEW.author_status,
        NEW.activity_rate,
        NEW.nb_outings,
        NEW.autonomy,
        NEW.age,
        NEW.gender,
        NEW.previous_injuries,
        true
    )
);

CREATE OR REPLACE RULE update_xreports AS ON UPDATE TO xreports DO INSTEAD 
(
    INSERT INTO app_xreports_archives
    (
        id,
        module,
        is_protected,
        redirects_to,
        lon,
        lat,
        geom_wkt,
        geom,
        elevation,
        date,
        activities,
        nb_participants,
        nb_impacted,
        severity,
        rescue,
        event_type,
        avalanche_level,
        avalanche_slope,
        author_status,
        activity_rate,
        nb_outings,
        autonomy,
        age,
        gender,
        previous_injuries,
        is_latest_version
    )
    VALUES
    (
        NEW.id,
        'xreports',
        NEW.is_protected,
        NEW.redirects_to,
        NEW.lon,
        NEW.lat,
        NEW.geom_wkt,
        NEW.geom,
        NEW.elevation,
        NEW.date,
        NEW.activities,
        NEW.nb_participants,
        NEW.nb_impacted,
        NEW.severity,
        NEW.rescue,
        NEW.event_type,
        NEW.avalanche_level,
        NEW.avalanche_slope,
        NEW.author_status,
        NEW.activity_rate,
        NEW.nb_outings,
        NEW.autonomy,
        NEW.age,
        NEW.gender,
        NEW.previous_injuries,
        true
    )
); 

CREATE OR REPLACE RULE insert_xreports_i18n AS ON INSERT TO xreports_i18n DO INSTEAD 
(
    INSERT INTO app_xreports_i18n_archives
    (
        id,
        culture,
        name,
        search_name,
        description,
        place,
        route_study,
        conditions,
        training,
        motivations,
        group_management,
        risk,
        time_management,
        safety,
        reduce_impact,
        increase_impact,
        modifications,
        other_comments,
        is_latest_version
    )
    VALUES
    (
        NEW.id,
        NEW.culture,
        NEW.name,
        NEW.search_name,
        NEW.description,
        NEW.place,
        NEW.route_study,
        NEW.conditions,
        NEW.training,
        NEW.motivations,
        NEW.group_management,
        NEW.risk,
        NEW.time_management,
        NEW.safety,
        NEW.reduce_impact,
        NEW.increase_impact,
        NEW.modifications,
        NEW.other_comments,
        true
    )
);

CREATE OR REPLACE RULE update_xreports_i18n AS ON UPDATE TO xreports_i18n DO INSTEAD 
(
    INSERT INTO app_xreports_i18n_archives
    (
        id,
        culture,
        name,
        search_name,
        description,
        place,
        route_study,
        conditions,
        training,
        motivations,
        group_management,
        risk,
        time_management,
        safety,
        reduce_impact,
        increase_impact,
        modifications,
        other_comments,
        is_latest_version
    )
    VALUES
    (
        NEW.id,
        NEW.culture,
        NEW.name,
        NEW.search_name,
        NEW.description,
        NEW.place,
        NEW.route_study,
        NEW.conditions,
        NEW.training,
        NEW.motivations,
        NEW.group_management,
        NEW.risk,
        NEW.time_management,
        NEW.safety,
        NEW.reduce_impact,
        NEW.increase_impact,
        NEW.modifications,
        NEW.other_comments,
        true
    )
);

-------------------------------------------------------------------------------
-- Triggers

-- Set is_latest_version to false before adding a new version.
CREATE TRIGGER update_xreports_latest_version BEFORE INSERT ON app_xreports_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version(app_xreports_archives);
CREATE TRIGGER update_xreports_i18n_latest_version BEFORE INSERT ON app_xreports_i18n_archives FOR EACH ROW EXECUTE PROCEDURE reset_latest_version_i18n(app_xreports_i18n_archives);

-- Trigger qui met à jour la table des versions pour les xreports quand on fait une modif d'un xreport dans une langue --
-- executé en premier par Sf, avant le trigger insert_xreports_archives.... --
CREATE TRIGGER insert_xreports_i18n_archives AFTER INSERT ON app_xreports_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions_i18n();
-- Trigger qui met à jour la table des versions pour les xreports quand on fait une modif "chiffres" sur un xreport --
CREATE TRIGGER insert_xreports_archives AFTER INSERT ON app_xreports_archives FOR EACH ROW EXECUTE PROCEDURE update_documents_versions();

-- Trigger that updates the geom columns (wkt to/from wkb conversion)
CREATE TRIGGER insert_geom_xreports BEFORE INSERT OR UPDATE ON app_xreports_archives FOR EACH ROW EXECUTE PROCEDURE update_geom_pt();

CREATE TRIGGER update_search_name_xreports_i18n_archives BEFORE INSERT OR UPDATE ON app_xreports_i18n_archives FOR EACH ROW EXECUTE PROCEDURE update_search_name();


-------------------------------------------------------------------------------
-- Association with xreports

INSERT INTO app_association_types (type) VALUES ('rx'); -- 'rx' = route-xreport (main = route)
INSERT INTO app_association_types (type) VALUES ('tx'); -- 'tx' = site-xreport (main = site)
INSERT INTO app_association_types (type) VALUES ('ox'); -- 'ox' = outing-xreport (main = outing)
INSERT INTO app_association_types (type) VALUES ('ux'); -- 'ux' = user-xreport (main = user)
INSERT INTO app_association_types (type) VALUES ('xc'); -- 'xc' = xreport-article (main = xreport)
INSERT INTO app_association_types (type) VALUES ('xi'); -- 'xi' = xreport-image (main = xreport)


COMMIT;

