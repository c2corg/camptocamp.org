ALTER TABLE ONLY app_summits_archives DROP CONSTRAINT summits_archives_pkey;
ALTER TABLE ONLY app_summits_i18n_archives DROP CONSTRAINT summits_i18n_archives_pkey;
DROP INDEX app_summits_archives_elevation_idx;

-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', '1ère Tour de Queyrellin');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2600, 1, '45.0322151345128', '6.51599977466025', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', '3eme Tour de Queyrellin');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2936, 1, '45.0354557395298', '6.51516314188782', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Abu Aina Tower');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1500, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Abu Judaidah, N. Gendarme');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1286, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aconcagua');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 6962, 1, '-32.653285', '-70.012387', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Adaouda');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1858, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Adelboden - Bonderfälle');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1600, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aderspitze');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2990, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Adna Gora ...');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2800, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Adriane');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1706, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Adula - Rheinwaldhorn');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3402, 1, '46.4938343479561', '9.04021570887337', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Agin kuk');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1000, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ago del Torrone (Cleopatra\' s Needle)');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3234, 1, '46.291383381678', '9.70069567713268', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ago di Sciora');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3205, 1, '46.3016000360491', '9.62599285927005', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aguglia di Cala Goloritzè');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 200, 1, '40.107328', '9.690069', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aguilles Rouges d\'Arolla - Sommet Central');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3646, 1, '46.0553519095131', '7.43375393358029', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Agulla del Portarró');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2673, 1, '42.57272', '0.979432', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Agulles de Comalestorres - Agulla Jordi Pujol');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2100, 1, '42.586266', '0.845701', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Agulles de Comalestorres - Cuarta Agulla');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2400, 1, '42.588471', '0.844354', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Agulles de Comalestorres - Punta Ferrusola');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2000, 1, '42.585939', '0.846408', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Agulles de Dellui');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2700, 1, '42.557992', '0.94333', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ahrner Kopf / Cima del Vento');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3051, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiglière Épaule SW');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3200, 1, '44.81033', '6.41477', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiglun');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 950, 1, '43.864009372673', '6.90919501104636', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Carrée');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3716, 1, '45.93972222', '6.96722222', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Centrale d\'Arves');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3513, 1, '45.1272207814273', '6.33655513550089', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Centrale de la Saussaz');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3361, 1, '45.1133522377182', '6.31865552850651', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Centrale du Soreiller');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3338, 1, '44.96719', '6.2418', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Chenavier');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3799, 1, '45.92583333', '7.00555556', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Croche');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2487, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Croux');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3256, 1, '45.81027778', '6.885', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Dibona');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3131, 1, '44.9626', '6.243', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Doran');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3041, 1, '45.2516867047921', '6.67852560945095', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Grive');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2732, 1, '45.5495369559416', '6.80406460192176', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Inférieure de la Floria');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2850, 1, '45.971389', '6.864722', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Joseph Gaillard');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2757, 1, '45.2448944086434', '6.14300213579709', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Large de Mary');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2857, 1, '44.56952', '6.85695', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Martin');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2887, 1, '45.9947781259751', '6.89406555366761', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Méridionale d\'Arves');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3514, 1, '45.1229855645174', '6.33442159917321', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille N de Tré la Tête');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3892, 1, '45.79777778', '6.81138889', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille N du Van');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2572, 1, '46.059444', '6.919722', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Noire');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2869, 1, '45.0948860014864', '6.48034604504362', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Noire de Peuterey');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3772, 1, '45.815', '6.89305556', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Noire de Pramecou');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2977, 1, '45.4478389613037', '6.86606200367524', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Occidentale de la Saussaz');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3340, 1, '45.1116508130121', '6.3114741972027', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Occidentale du Soreiller');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3280, 1, '44.96636', '6.23835', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Orientale de Tré-la-Tête');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3895, 1, '45.79388889', '6.81805556', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Orientale du Soreiller');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3380, 1, '44.96787', '6.24867', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Pers');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3386, 1, '45.4263482894635', '7.07105315669676', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Pierre André');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2812, 1, '44.57375', '6.85322', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Pierre Joseph - Pointe 2842');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2842, 1, '45.90388889', '6.97861111', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Pierre-Alain');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2784, 1, '45.88694444', '6.92555556', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Purtscheller');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3478, 1, '45.991376127539', '7.01238067356146', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Rouge');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3227, 1, '45.5517813378213', '6.84857525731703', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Rouge de Varan');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2636, 1, '45.961944', '6.685', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Rouge du Triolet');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3289, 1, '45.8995200148047', '7.04110665397732', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille SE de Tré la Tête');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3930, 1, '45.79472222', '6.81472222', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Sans Nom');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2600, 1, '45.65555556', '6.66055556', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Septentrionale d\'Arves');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3364, 1, '45.1323223385932', '6.34120088210311', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Tourelle');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3020, 1, '46.003976011722', '7.05657182552712', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Verte');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 4122, 1, '45.9344816157128', '6.97013815561148', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille Verte du Chinaillon');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2045, 1, '45.9824457478854', '6.43257206651187', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille d\'Ancelle');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2367, 1, '44.60958', '6.2477', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille d\'Argentière');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3237, 1, '45.1063863875914', '6.36036746861455', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille d\'Argentière');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3901, 1, '45.9597862103337', '7.02025007611683', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille d\'Artanavaz');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3071, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille d\'Aujon, p. 2389');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2389, 1, '45.985833', '6.679444', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille d\'Azrou');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1700, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille d\'Entre-Pierroux');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3290, 1, '44.89059', '6.17671', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille d\'Entrèves');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3600, 1, '45.84361111', '6.91583333', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille d\'Olle');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2885, 1, '45.2450972581336', '6.13805987313808', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille d\'Orcières');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2793, 1, '44.64736', '6.33574', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille d\'Orny');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3150, 1, '46.0030058019752', '7.05431964874278', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Bionnassay');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 4052, 1, '45.8360070091301', '6.81818467453607', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Blaitière');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3522, 1, '45.89916667', '6.91277778', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Blaitière - Face W');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3507, 1, '45.89972222', '6.91194444', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Blaitière - Pilier Rouge');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3170, 1, '45.90222222', '6.90944444', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Blaitière - Tours de l\'arête SE');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3040, 1, '45.89666667', '6.91527778', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Borderan');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2492, 1, '45.8862', '6.47731', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Boveire');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3629, 1, '45.9768699783715', '7.26728601592995', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Bénevise');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1000, 1, '44.73556', '5.52861', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Bérard');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2550, 1, '45.9941412690473', '6.85327449202142', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Capdepon');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2750, 1, '45.2471950711424', '6.14781353780436', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Clarabide');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2679, 1, '42.695833', '0.45', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Coste Rouge');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3252, 1, '44.895', '6.35744', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Coupet');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2639, 1, '45.66166667', '6.74472222', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Grabe');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1650, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Laisse');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2879, 1, '45.1864890668334', '6.16037987608675', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Leschaux');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3759, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Marcieu');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2906, 1, '45.2448246866979', '6.12922852797511', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Mex');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1866, 1, '46.1895461792624', '6.98121773037653', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Montaubert');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1847, 1, '45.8236', '6.30859', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Morges');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2985, 1, '44.799', '6.25795', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Pierre Joseph  Pointe 2940m');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2940, 1, '45.9025', '6.98027778', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Polset');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3534, 1, '45.2759887435263', '6.63418805224707', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Praina');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2607, 1, '45.6594888211224', '6.75393793823929', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Praz Torrent');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2573, 1, '46.005833', '6.899722', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Péclet');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3561, 1, '45.2813696370058', '6.62402912151899', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Roc');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3406, 1, '45.90138889', '6.92', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Rochefort');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 4001, 1, '45.86222222', '6.95944444', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Roselette');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2384, 1, '45.7707205933648', '6.69072598006198', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Salenton');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2671, 1, '46.0089611985635', '6.85403216740409', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Scolette / Pierre Menue');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3506, 1, '45.1598402336815', '6.76790621225082', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Sialouze');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3576, 1, '44.88811', '6.38494', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Talèfre');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3730, 1, '45.89972222', '7.00388889', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Terrassin');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2836, 1, '45.6661620288236', '6.72745294047644', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Toule');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3534, 1, '45.8475', '6.92138889', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Tricot');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3665, 1, '45.83694444', '6.80777778', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de Varan');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2544, 1, '45.9579428942463', '6.68304645876139', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de l\'Eboulement');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3599, 1, '45.89333333', '7.00333333', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de l\'Encrenaz');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2887, 1, '45.9956928945215', '6.89534711173281', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de l\'Index');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2595, 1, '45.9675', '6.87', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de l\'M');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2844, 1, '45.91222222', '6.91277778', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de l\'Olan');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3360, 1, '44.87729', '6.20008', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de l\'Épaisseur');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3230, 1, '45.1325611067697', '6.35577218174576', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de l\'Épéna');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3421, 1, '45.4143355563809', '6.8171156627291', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Balme');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2696, 1, '45.4708169164353', '6.36989222887896', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Bérangère');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3425, 1, '45.802617419652', '6.77915516934855', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Cabane');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2999, 1, '46.0066345693563', '7.06605441967607', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Canalona');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2540, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Charlanon');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2549, 1, '45.9542930959892', '6.85037396490911', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Combe');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2747, 1, '45.2474822215057', '6.14534708819402', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Floria');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2888, 1, '45.9761405743004', '6.8689759346737', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Gandolière');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3050, 1, '44.97171', '6.28416', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Glière');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2836, 1, '45.9701409352924', '6.86377496977264', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Lex Blanche');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3697, 1, '45.78583333', '6.80333333', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Mesure');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2812, 1, '46.0025', '6.896111', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Nova');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2893, 1, '45.650711676456', '6.67624401800369', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Perséverance');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2901, 1, '45.994444', '6.893611', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Petite Sassière');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3672, 1, '45.5120245023633', '7.00144216020411', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la République');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3305, 1, '45.90555556', '6.92138889', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Tsa');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3668, 1, '46.0202563909885', '7.52273617694906', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Tête Plate');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2944, 1, '45.9905463283313', '6.8804536124868', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille de la Vanoise');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2796, 1, '45.3949398837682', '6.77941558022964', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Arias');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3402, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Calvaires');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2322, 1, '45.8898', '6.4714', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Chamois');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2902, 1, '45.9943271444324', '6.89196629098275', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Ciseaux');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3479, 1, '45.89777778', '6.9125', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Espères');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3059, 1, '44.77208', '6.28841', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Glaciers');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3817, 1, '45.7784026724285', '6.80268986435662', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Grands');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3086, 1, '46.00333333', '6.98861111', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Grands Montets');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3297, 1, '45.94805556', '6.96', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Lanchettes');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3073, 1, '45.77027778', '6.76944444', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Marmes');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3046, 1, '44.89502', '6.0992', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Pélerins');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3318, 1, '45.89416667', '6.90222222', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Saints Pères');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3360, 1, '45.2761121821083', '6.61462669175444', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Sasses');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3014, 1, '45.859368185767', '7.11813060193361', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille des Veis');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3024, 1, '45.70166667', '6.83027778', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Belvédère');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2965, 1, '45.9877337839747', '6.87387501926696', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Borgne');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3138, 1, '45.3138720031155', '6.61286547474258', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Bouchet de Serraval');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2170, 1, '45.793', '6.40449', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Chambeyron');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3412, 1, '44.54753', '6.85615', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Chardonnet');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3824, 1, '45.9689710898504', '7.00146121810636', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Charmo');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2655, 1, '46.0481635646371', '6.89505698264926', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Clapet');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2615, 1, '45.6632062359539', '6.82165729922396', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Franchet');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2809, 1, '45.4723692891031', '6.97364473648044', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Fruit');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3051, 1, '45.355502297355', '6.63111136792951', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Goléon');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3427, 1, '45.1033488675914', '6.32633328640445', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Goléon - Antécime 3058m');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3058, 1, '45.0971875125785', '6.33641444471804', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Grand Fond');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2920, 1, '45.6651171869907', '6.66609819251604', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Grand Laus');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2801, 1, '44.90627', '6.42042', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Génépi');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3263, 1, '45.9995780720286', '7.0024680906981', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Midi');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3842, 1, '45.878701951034', '6.8874161683585', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Midi des Grands');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3319, 1, '46.00027778', '7.01166667', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Moine');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3412, 1, '45.91666667', '6.96138889', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Peigne');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3192, 1, '45.895', '6.9', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Pissoir');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3450, 1, '45.9969288251569', '7.01064649010497', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Plan');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3673, 1, '45.8917693590446', '6.90726495787961', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Plat de la Selle');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3597, 1, '44.9645', '6.2278', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Refuge');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3057, 1, '45.94777778', '7.00666667', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Saint Esprit');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3419, 1, '45.5357798247487', '6.84678611239521', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Tacul');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3444, 1, '45.8846791371509', '6.96086567482562', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Tour');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3542, 1, '45.9952034869585', '7.01112465662528', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille du Vélan');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3634, 1, '45.8980680285507', '7.24408532551023', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille qui Remue');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3724, 1, '45.92444444', '7.00694444', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguille à Bochard');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2681, 1, '45.94666667', '6.93833333', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles Crochues');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2837, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles Crochues');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2837, 1, '45.9819689377789', '6.8741540023033', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles Crochues - Sommet S');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2840, 1, '45.9804985626764', '6.8733433922845', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles Dorées - Aiguille Sans Nom');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3444, 1, '45.983890671784', '7.03899536813391', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles Dorées - Aiguille de la Varappe');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3519, 1, '45.9827827754409', '7.03357104671093', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles Marbrées');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3535, 1, '45.85166667', '6.94027778', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles Rouges d\'Arolla - Sommet S');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3584, 1, '46.0512138668091', '7.43201018521036', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles d\'en Beys');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2600, 1, '42.616667', '1.948611', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles de Baulmes');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1559, 1, '46.7908632851554', '6.47597005461121', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles de Castet-Abarca');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2569, 1, '42.85', '-0.185833', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles de Chabrieres');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2403, 1, '44.57903', '6.33109', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles de Chamonix');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3673, 1, '45.89138889', '6.90666667', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles de Travessani');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2700, 1, '42.610225', '0.887752', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles de l\'Argentière');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2915, 1, '45.2443898769479', '6.13141390823098', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles de la Grande Moendaz');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2696, 1, '45.3461601009542', '6.43492164276182', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles de la Lé');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3179, 1, '46.1005813756943', '7.60165016138508', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles de la Pennaz');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2688, 1, '45.74444444', '6.69972222', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles des Angroniettes');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2880, 1, '45.8713048590955', '7.09381265208565', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles du Chabarrou');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2808, 1, '42.792778', '-0.153611', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguilles du Mont');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2133, 1, '45.7958', '6.4128', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguillette Saint Michel');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1700, 1, '45.3643', '5.90341', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguillette de Seyne');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2610, 1, '44.3351754473031', '6.45225159717995', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguillette des Houches');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2285, 1, '45.9213754762942', '6.80529310750505', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aiguillette du Lauzet');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2747, 1, '45.0212638305757', '6.48323564077505', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ailefroide - Contreforts du Pelvoux');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2000, 1, '44.88872', '6.4401', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ailefroide - Dalles d\'Ailefroide');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2200, 1, '44.88486', '6.44032', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ailefroide - La Draye');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1800, 1, '44.89013', '6.44828', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ailefroide - La Poire d\'Ailefroide');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2000, 1, '44.89914', '6.43852', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ailefroide - Paroi de la Fissure');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1850, 1, '44.88132', '6.44545', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ailefroide - Paroi des Ribeyrettes');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2000, 1, '44.87915', '6.46369', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ailefroide - Pointe Fourastier');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3908, 1, '44.88815', '6.36262', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ailefroide Centrale');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3927, 1, '44.88793', '6.3594', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ailefroide Occidentale');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3954, 1, '44.88477', '6.35594', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ailefroide Orientale');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3847, 1, '44.88804', '6.36869', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Akioud');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 4030, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ala Izquierda');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 5532, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alamon');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1900, 1, '46.3510256678149', '6.83288515762487', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alaça');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3588, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Albaron');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3637, 1, '45.3334560361776', '7.10187573476077', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Albiolino');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2921, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Albristhorn');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2762, 1, '46.4971258686204', '7.48933171434861', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Albristhubel');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2124, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aletschhorn');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 4195, 1, '46.465124157735', '7.99371608107491', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Allalinhorn');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 4027, 1, '46.0460283928679', '7.89466075675782', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alllalinpass');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3564, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alpamayo');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 5947, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alpe Albio');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 585, 1, '45.9087012067941', '8.96253196712039', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alpe Prabello');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1602, 1, '45.9584669641457', '9.41491257118802', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alpe d\'Huez');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2450, 1, '45.1172743621852', '6.09157793759578', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alpet');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2865, 1, '44.59251', '6.88401', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alphubel');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 4206, 1, '46.0629254402632', '7.86394259320193', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alphubeljoch');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3782, 1, '46.05371784803', '7.87467240298947', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alpiglemären');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2044, 1, '46.6886851208839', '7.4076513364983', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alplesspitze');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3149, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Alt del Griu');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2874, 1, '42.525556', '1.651944', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Altels');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3629, 1, '46.4289400757934', '7.67844250484183', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Altmann (sommet N)');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2386, 1, '47.2410572015741', '9.36768951593586', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ama Dablam');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 6856, 1, '27.862825', '86.861293', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ambrevetta - Tardevant');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2501, 1, '45.9267015545635', '6.52582151249402', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Amelier');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2002, 1, '46.5494331106394', '7.24456638747259', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ammertenspitz');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2613, 1, '46.4333973168957', '7.52476595322706', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ancohuma');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 6427, 1, '-15.854027', '-68.541925', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aneto');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3404, 1, '42.631282', '0.654999', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Angour');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3800, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Anika kuk');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 712, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ankenbälli');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3601, 1, '46.6116163408816', '8.14564216674675', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ankestock');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2155, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ankogel');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3250, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Antelao');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3264, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Antuco');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2985, 1, '-37.41044', '-71.350829', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Antécime S du Créton du Midi');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2785, 1, '45.8146538535157', '7.10033134934373', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Antécime S du Mont Chénaille');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2991, 1, '45.8544866914224', '7.25546597064767', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aouille Tseuque');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3554, 1, '45.9302069025262', '7.44281564035033', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aragats');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3920, 1, '40.510041', '44.193021', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arappkopf');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2195, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arbizon');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2834, 1, '42.876111', '0.273333', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ard el Mezrab');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3041, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Ardève');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1501, 1, '46.1964193504604', '7.19987929294004', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Areuapass');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2509, 1, '46.5100540862571', '9.27854865097132', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Argentera - Cime S');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3297, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Argentera - Gelas di Lourousa');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3261, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Argentière-Arolla J1');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3321, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arias');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3200, 1, '44.89565', '6.17709', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arnergale');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2623, 1, '46.4169938393901', '8.22788830093085', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arnihaaggen');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2212, 1, '46.7885641388274', '8.06906732440486', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aroser Rothorn');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2980, 1, '46.7377945624121', '9.61394075748481', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arpelistock');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3035, 1, '46.343872991411', '7.31526327555164', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arre Sourins');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2614, 1, '42.925278', '-0.333611', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Artesonraju');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 6025, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Artharnolatze');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1530, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arvigrat');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2014, 1, '46.8871867325044', '8.33435314083902', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arête Fabien');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2581, 1, '46.1366295710543', '6.90857631136716', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arête Plate');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2870, 1, '45.9925', '6.88472222', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arête de Belair ou Montagne de Pertuis');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2162, 1, '46.2629796147701', '6.77171261980657', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arête de Tcholeire - Babylone/ Tête Verte');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2857, 1, '45.8703080551078', '7.19769815144786', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arête de l\'Argentine');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2421, 1, '46.2735844091833', '7.13276993016222', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arête de la Besse');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2212, 1, '45.9251', '6.54084', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arête de la Grande Autane');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2492, 1, '44.6405', '6.29278', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arête de la Tsa');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3500, 1, '46.0233231913927', '7.52363187579582', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arête des Ecandies');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2870, 1, '46.0151250267569', '7.03945086766331', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arête du Charmoz');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2366, 1, '46.0452846667571', '6.91202188813666', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arêtes d\'Allemont');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2770, 1, '45.1534188475863', '5.99624268574873', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arêtes de la Bruyère');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2611, 1, '45.0362618022129', '6.47811161564026', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Arêtes du Sapey');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1700, 1, '45.9279', '6.32025', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Asulkan Pass');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2341, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Atxerito');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2362, 1, '42.891667', '-0.736944', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Auf den Vertainen / Alti di Vertana');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3049, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Augstberg');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2449, 1, '46.7560158435143', '9.74282731131392', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Augstbordhorn');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2972, 1, '46.2354255203959', '7.79422359676805', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Augstenhüreli');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3027, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Augstkummenhorn');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3419, 1, '46.0696682258534', '8.02171018481494', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aup de Véran');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2437, 1, '45.9831401476012', '6.69244050033781', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aupillon');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2916, 1, '44.44873', '6.57795', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Aussemont');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 1793, 1, '46.400699294062', '7.03406118124629', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Auto Vallonasso');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2885, 1, '44.45099', '6.93746', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Avachinsky');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2751, 1, '53.255793', '158.833928', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Avanza');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2489, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Baba Grande');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2160, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Bacanère');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2193, 1, '42.846111', '0.675833', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Bachberggrat');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2218, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Bachimala / Pïc Schrader');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 3174, 1, '42.698889', '0.395278', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Baerennock');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2587, 1, NULL, NULL, false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Baisse Niré');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2560, 1, '44.0967574404163', '7.41119342374589', false);
COMMIT;


-- Insertion of summit in fr
BEGIN;
INSERT INTO app_history_metadata (user_id, is_minor, comment) VALUES (2, FALSE, 'imported from v4');
INSERT INTO summits_i18n (id, culture, name) VALUES (nextval('documents_id_seq'::text), 'fr', 'Baisse de Barel');
INSERT INTO summits (id, elevation, summit_type, lat, lon, is_protected) VALUES (currval('documents_id_seq'::text), 2242, 1, '44.1963984658959', '6.85850074581148', false);
COMMIT;

ALTER TABLE ONLY app_summits_archives ADD CONSTRAINT summits_archives_pkey PRIMARY KEY (summit_archive_id);
ALTER TABLE ONLY app_summits_i18n_archives ADD CONSTRAINT summits_i18n_archives_pkey PRIMARY KEY (summit_i18n_archive_id);
CREATE INDEX app_summits_archives_elevation_idx ON app_summits_archives USING btree (elevation);

VACUUM ANALYSE;
