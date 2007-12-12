-- Creates the remember_keys tables, views, rules, triggers ...
--
-- $Id: db_remember_keys.sql 2358 2007-11-17 16:17:43Z fvanderbiest $ --
--

--
CREATE SEQUENCE app_remember_keys_id_seq INCREMENT BY 1 NO MAXVALUE MINVALUE 1 CACHE 1;

-- Some data do not require versioning
CREATE TABLE app_remember_keys (
    id integer NOT NULL DEFAULT nextval('app_remember_keys_id_seq'::text),   
    user_id integer NOT NULL,
    remember_key character varying(32) NOT NULL,
    ip_address character varying(15),
    created_at timestamp without time zone NOT NULL DEFAULT NOW()
);
ALTER TABLE app_remember_keys ADD CONSTRAINT users_pkey PRIMARY KEY (id);

CREATE INDEX app_remember_key_idx ON app_remember_keys USING btree (remember_key); 