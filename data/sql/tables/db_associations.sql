-- Creates the associations tables ... ex : routes with summits
--
-- $Id: db_associations.sql 2154 2007-10-23 16:16:42Z fvanderbiest $ --
--


CREATE TABLE app_association_types (
    type char(2) NOT NULL
);
ALTER TABLE app_association_types ADD CONSTRAINT associations_pkey PRIMARY KEY (type);
COMMENT ON TABLE app_association_types IS 'This table is meant to keep a record of all kinds of documents associations';

-- association with outings
INSERT INTO app_association_types (type) VALUES ('ro'); -- 'ro' = route-outing (main = route)
INSERT INTO app_association_types (type) VALUES ('to'); -- 'to' = site-outing (main = site) * : was 'so'
INSERT INTO app_association_types (type) VALUES ('uo'); -- 'uo' = user-outing (main = user)

-- association with summits
INSERT INTO app_association_types (type) VALUES ('ss'); -- 'ss' = summit-summit (main = left column)
INSERT INTO app_association_types (type) VALUES ('bs'); -- 'bs' = book-summit (main = book)

-- association with routes
INSERT INTO app_association_types (type) VALUES ('rr'); -- 'rr' = route-route (main = left column)
INSERT INTO app_association_types (type) VALUES ('sr'); -- 'sr' = summit-route (main = summit)
INSERT INTO app_association_types (type) VALUES ('br'); -- 'br' = book-route (main = book)
INSERT INTO app_association_types (type) VALUES ('hr'); -- 'hr' = hut-route (main = hut)
INSERT INTO app_association_types (type) VALUES ('pr'); -- 'pr' = parking-route (main = parking)
INSERT INTO app_association_types (type) VALUES ('tr'); -- 'tr' = site-route (main = site) * : was 'st'

-- association with huts
INSERT INTO app_association_types (type) VALUES ('bh'); -- 'bh' = book-hut (main = book)
INSERT INTO app_association_types (type) VALUES ('ph'); -- 'ph' = parking-hut (main = parking)
INSERT INTO app_association_types (type) VALUES ('sh'); -- 'sh' = summit-hut (main = summit)

-- association with parkings
INSERT INTO app_association_types (type) VALUES ('pp'); -- 'pp' = parking-parking (main = left column)
INSERT INTO app_association_types (type) VALUES ('pf'); -- 'pf' = parking-product (main = parking)

-- associations with sites
INSERT INTO app_association_types (type) VALUES ('tt'); -- 'tt' = site-site * : was 'ii'
INSERT INTO app_association_types (type) VALUES ('st'); -- 'st' = summit-site (main = summit) * : was 'us'
INSERT INTO app_association_types (type) VALUES ('ht'); -- 'ht' = hut-site (main = hut) * : was 'hs'
INSERT INTO app_association_types (type) VALUES ('pt'); -- 'pt' = parking-site (main = parking) * : was 'ps'
INSERT INTO app_association_types (type) VALUES ('bt'); -- 'bt' = book-site (main = book)

-- association with xreports
INSERT INTO app_association_types (type) VALUES ('rx'); -- 'rx' = route-xreport (main = route)
INSERT INTO app_association_types (type) VALUES ('tx'); -- 'tx' = site-xreport (main = site)
INSERT INTO app_association_types (type) VALUES ('ox'); -- 'ox' = outing-xreport (main = outing)
INSERT INTO app_association_types (type) VALUES ('ux'); -- 'ux' = user-xreport (main = user)

-- associations with articles
INSERT INTO app_association_types (type) VALUES ('cc'); -- 'cc' = article-article
INSERT INTO app_association_types (type) VALUES ('sc'); -- 'sc' = summit-article (main = summit)
INSERT INTO app_association_types (type) VALUES ('bc'); -- 'bc' = book-article (main = book)
INSERT INTO app_association_types (type) VALUES ('hc'); -- 'hc' = hut-article (main = hut)
INSERT INTO app_association_types (type) VALUES ('oc'); -- 'oc' = outing-article (main = outing)
INSERT INTO app_association_types (type) VALUES ('rc'); -- 'rc' = route-article (main = route)
INSERT INTO app_association_types (type) VALUES ('tc'); -- 'tc' = site-article (main = site) * (means: modified), was : 'ic'
INSERT INTO app_association_types (type) VALUES ('uc'); -- 'uc' = user-article (main = user)
INSERT INTO app_association_types (type) VALUES ('pc'); -- 'pc' = parking-article (main = parking)
INSERT INTO app_association_types (type) VALUES ('fc'); -- 'fc' = product-article (main = product)
INSERT INTO app_association_types (type) VALUES ('xc'); -- 'xc' = xreport-article (main = xreport)

-- associations with images
--was : INSERT INTO app_association_types (type) VALUES ('di'); -- 'di' = document-image (main = document, in that case, image is linked)
INSERT INTO app_association_types (type) VALUES ('ii'); -- 'ii' = image-image
INSERT INTO app_association_types (type) VALUES ('ai'); -- 'ai' = area-image (main = area)
INSERT INTO app_association_types (type) VALUES ('mi'); -- 'mi' = map-image (main = map)
INSERT INTO app_association_types (type) VALUES ('ci'); -- 'ci' = article-image (main = article)
INSERT INTO app_association_types (type) VALUES ('bi'); -- 'bi' = book-image (main = book)
INSERT INTO app_association_types (type) VALUES ('hi'); -- 'hi' = hut-image (main = hut)
INSERT INTO app_association_types (type) VALUES ('pi'); -- 'pi' = parking-image (main = parking)
INSERT INTO app_association_types (type) VALUES ('oi'); -- 'oi' = outing-image (main = outing)
INSERT INTO app_association_types (type) VALUES ('ri'); -- 'ri' = route-image (main = route)
INSERT INTO app_association_types (type) VALUES ('ti'); -- 'ti' = site-image (main = site)
INSERT INTO app_association_types (type) VALUES ('si'); -- 'si' = summit-image (main = summit)
INSERT INTO app_association_types (type) VALUES ('ui'); -- 'ui' = user-image (main = user)
INSERT INTO app_association_types (type) VALUES ('fi'); -- 'fi' = product-image (main = product)
INSERT INTO app_association_types (type) VALUES ('wi'); -- 'wi' = portal-image (main = portal)
INSERT INTO app_association_types (type) VALUES ('xi'); -- 'xi' = xreport-image (main = xreport)


CREATE TABLE app_documents_associations (
    main_id integer NOT NULL,
    linked_id integer NOT NULL,
    type char(2) NOT NULL 
);

-- With this, unicity is garanteed and a bicolumn index is created:
ALTER TABLE app_documents_associations ADD CONSTRAINT documents_associations_pkey PRIMARY KEY (main_id,linked_id); 
-- This is for data integrity:
ALTER TABLE app_documents_associations ADD CONSTRAINT fk_app_documents_associations_type FOREIGN KEY (type) REFERENCES app_association_types(type);
-- This is for speed:
CREATE INDEX app_documents_associations__type__idx ON app_documents_associations USING btree (type);
CREATE INDEX app_documents_associations__linked__idx ON app_documents_associations USING btree (linked_id);


-- geo associations:
CREATE TABLE app_geo_association_types (
    type char(2) NOT NULL
);
ALTER TABLE app_geo_association_types ADD CONSTRAINT geo_associations_types_pkey PRIMARY KEY (type);
COMMENT ON TABLE app_geo_association_types IS 'This table is meant to keep a record of all kinds of documents associations based on their geometry. It is thus used for filtering on ranges.';

INSERT INTO app_geo_association_types (type) VALUES ('dr'); -- 'dr' = document-range (main = left column) thus = map-range (in that case, map is main !) -> we should thus prevent maps from being associated with ranges, dept, and countries with types 'dr', 'dd', 'dc' : only 'dm' type should handle these cases
INSERT INTO app_geo_association_types (type) VALUES ('dd'); -- 'dd' = document-department (main = left column) thus = area-map
INSERT INTO app_geo_association_types (type) VALUES ('dc'); -- 'dc' = document-country (main = left column) thus = area-map
INSERT INTO app_geo_association_types (type) VALUES ('dv'); -- 'dv' = document-valley (main = left column) thus = area-map
INSERT INTO app_geo_association_types (type) VALUES ('dm'); -- 'dm' = document-map (main = document, in that case, map is linked)

CREATE TABLE app_geo_associations (
    main_id integer NOT NULL,
    linked_id integer NOT NULL,
    type char(2) NOT NULL 
);

-- With this, unicity is garanteed and a bicolumn index is created:
ALTER TABLE app_geo_associations ADD CONSTRAINT geo_associations_pkey PRIMARY KEY (main_id,linked_id); 
-- This is for data integrity:
ALTER TABLE app_geo_associations ADD CONSTRAINT fk_app_geo_associations_type FOREIGN KEY (type) REFERENCES app_geo_association_types(type);
-- This is for speed:
CREATE INDEX app_geo_associations__type__idx ON app_geo_associations USING btree (type);
CREATE INDEX app_geo_associations__linked__idx ON app_geo_associations USING btree (linked_id);
