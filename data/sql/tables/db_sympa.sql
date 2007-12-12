-- Sympa (http://sympa.org) is the mailing lists management system.
-- This file contains SQL to create its DB tables
-- $Id: db_sympa.sql 1685 2007-09-19 10:13:57Z alex $

-- SQL statements retrieved from http://www.sympa.org/documentation/manual/doc-5.2.3/html/node9.html#SECTION00910000000000000000

CREATE TABLE user_table (
    email_user varchar (100) NOT NULL,
    gecos_user varchar (150),
    cookie_delay_user int4,
    password_user varchar (40),
    lang_user varchar (10),
    attributes_user varchar (255),
    CONSTRAINT ind_user PRIMARY KEY (email_user)
);

CREATE TABLE subscriber_table (
    list_subscriber varchar (50) NOT NULL,
    user_subscriber varchar (100) NOT NULL,
    date_subscriber timestamp with time zone NOT NULL DEFAULT NOW(),
    update_subscriber timestamp with time zone,
    visibility_subscriber varchar (20),
    reception_subscriber varchar (20),
    bounce_subscriber varchar (35),
    bounce_score_subscriber int4,
    comment_subscriber varchar (150),
    subscribed_subscriber smallint,
    included_subscriber smallint,
    include_sources_subscriber varchar(50),
    CONSTRAINT ind_subscriber PRIMARY KEY (list_subscriber, user_subscriber)
);
CREATE INDEX subscriber_idx ON subscriber_table (user_subscriber,list_subscriber);
