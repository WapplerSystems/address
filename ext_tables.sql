#
# Table structure for table 'tx_address_domain_model_address'
#
CREATE TABLE tx_address_domain_model_address
(
	title                 tinytext,
	first_name            tinytext,
	middle_name           tinytext,
	last_name             tinytext,
	birthday              int(11)          DEFAULT NULL,
	email                 varchar(255)     DEFAULT ''  NOT NULL,
	abbreviation          varchar(30)      DEFAULT ''  NOT NULL,
	phone                 varchar(30)      DEFAULT ''  NOT NULL,
	mobile                varchar(30)      DEFAULT ''  NOT NULL,
	www                   varchar(255)     DEFAULT ''  NOT NULL,
	address               tinytext,
	building              varchar(20)      DEFAULT ''  NOT NULL,
	room                  varchar(15)      DEFAULT ''  NOT NULL,
	company               varchar(255)     DEFAULT ''  NOT NULL,
	position              varchar(255)     DEFAULT ''  NOT NULL,
	city                  varchar(255)     DEFAULT ''  NOT NULL,
	zip                   varchar(20)      DEFAULT ''  NOT NULL,
	region                varchar(255)     DEFAULT ''  NOT NULL,
	country               varchar(128)     DEFAULT ''  NOT NULL,
	fax                   varchar(30)      DEFAULT ''  NOT NULL,
	keywords              text,
	description           text,
	skype                 varchar(255)     DEFAULT '',
	twitter               varchar(255)     DEFAULT '',
	facebook              varchar(255)     DEFAULT '',
	linkedin              varchar(255)     DEFAULT '',
	latitude              decimal(9, 6)    DEFAULT NULL,
	longitude             decimal(9, 6)    DEFAULT NULL,
	gender                varchar(1)       DEFAULT ''  NOT NULL,
	istopaddress          tinyint(4)       DEFAULT '0' NOT NULL,
	archive               int(11)          DEFAULT '0' NOT NULL,
	direct_contact        tinyint(1)       DEFAULT '0' NOT NULL,
	url                   text,
	type                  varchar(100)     DEFAULT '0' NOT NULL,
	academic_title        varchar(25)      DEFAULT '',
	append_academic_title tinyint(4)       DEFAULT '0' NOT NULL,
	teaser                text,
	bodytext              text,
	detail_pid            int(11)          DEFAULT '0' NOT NULL,
	related_links         tinytext,
	related               int(11)          DEFAULT '0' NOT NULL,
	related_from          int(11)          DEFAULT '0' NOT NULL,
	media                 int(11) unsigned DEFAULT '0',
	custom_marker_icon    int(11) unsigned DEFAULT '0',
	marker_icon           int(11) unsigned DEFAULT '0',
	marker_color          varchar(255)     DEFAULT '',
	content_elements      int(11)          DEFAULT '0' NOT NULL,
	related_news          int(11)          DEFAULT '0' NOT NULL,
	path_segment          tinytext,
	notes                 text,
	related_files         int(11) unsigned DEFAULT '0',
	contacts              int(11) unsigned DEFAULT '0',
	tags                  int(11)          DEFAULT '0' NOT NULL,

	sorting               int(11)          DEFAULT '0' NOT NULL,

	categories            int(11)          DEFAULT '0' NOT NULL,

	import_id             varchar(100)     DEFAULT ''  NOT NULL,
	import_source         varchar(100)     DEFAULT ''  NOT NULL,


	KEY import (import_id, import_source)
);


CREATE TABLE tx_address_domain_model_contact
(

	type    varchar(100) NOT NULL DEFAULT '',
	content text,

	address int(11)               DEFAULT '0' NOT NULL,

	notes   text
);


#
# Table structure for table 'tx_address_domain_model_address_ttcontent_mm'
#
#
CREATE TABLE tx_address_domain_model_address_ttcontent_mm
(
	uid_local   int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	sorting     int(11) DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);


#
# Table structure for table 'tx_address_domain_model_address_news_mm'
#
#
CREATE TABLE tx_address_domain_model_address_news_mm
(
	uid_local   int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	sorting     int(11) DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_address_domain_model_address_related_mm'
#
#
CREATE TABLE tx_address_domain_model_address_related_mm
(
	uid_local       int(11) DEFAULT '0' NOT NULL,
	uid_foreign     int(11) DEFAULT '0' NOT NULL,
	sorting         int(11) DEFAULT '0' NOT NULL,
	sorting_foreign int(11) DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_address_domain_model_link'
#
CREATE TABLE tx_address_domain_model_link
(
	parent      int(11) DEFAULT '0' NOT NULL,
	title       tinytext,
	description text,
	uri         text
);

#
# Table structure for table 'be_users'
#
CREATE TABLE be_users
(
	tx_address_categorymounts varchar(255) DEFAULT '' NOT NULL
);

#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content
(
	tx_address_related_address int(11) DEFAULT '0' NOT NULL,
	KEY index_addresscontent (tx_address_related_address)
);


#
# Table structure for table 'sys_file_reference'
#
CREATE TABLE sys_file_reference
(
	showinpreview tinyint(4) DEFAULT '0' NOT NULL
);
