#
# this is for REFERENCE only
# it tracks all the db changes made (new tables and alterations)
# do not run or otherwise use it. ignore it.
#

#
# table to cache preparsed posts
#
CREATE TABLE {$db_prefix}messages_cache (
  id_msg int(10) unsigned NOT NULL default '0',
  body mediumtext NOT NULL,
  style tinyint(2) NOT NULL default '0',
  lang  tinyint(2) NOT NULL default '0',
  updated int(4) NOT NULL default '0',
  PRIMARY KEY (id_msg, style, lang),
  KEY updated (updated)
) ENGINE=MyISAM;

#
# Table structure for table `likes`
#

CREATE TABLE {$db_prefix}likes (
	id_msg int(10) unsigned NOT NULL default '0',
  	id_user mediumint(8) unsigned NOT NULL default '0',
  	id_receiver mediumint(8) unsigned NOT NULL default '0',
  	updated int(4) unsigned NOT NULL default '0',
  	ctype tinyint(2) unsigned NOT NULL default '0',
  	PRIMARY KEY (id_msg, id_user, ctype),
  	KEY id_msg (id_msg),
  	KEY id_user (id_user),
  	KEY id_receiver (id_receiver),
  	KEY ordering (id_msg, updated)
) ENGINE=MyISAM;

#
# Table structure for table `like_cache`
#

CREATE TABLE {$db_prefix}like_cache (
	id_msg int(10) unsigned NOT NULL default '0',
  	likes_count int(4) unsigned NOT NULL default '0',
  	like_status varchar(255) NOT NULL default '',
  	updated int(4) NOT NULL default '0',
  	ctype tinyint(2) NOT NULL default '0',
  	PRIMARY KEY (id_msg, ctype)
) ENGINE=MyISAM;

#
# Table structure for table `prefixes`
#

CREATE TABLE {$db_prefix}prefixes (
	id_prefix smallint(5) unsigned NOT NULL auto_increment,
	name varchar(255) NOT NULL default '',
	boards varchar(200) NOT NULL default '',
	groups varchar(100) NOT NULL default '',
	PRIMARY KEY (id_prefix)
) ENGINE=MyISAM;

# 
# Tagging system (note: the table structure is compatible with SMFTags on purpose)
#

CREATE TABLE {db_prefix}tags (
    id_tag mediumint(8) NOT NULL auto_increment,
    tag tinytext NOT NULL,
    approved tinyint(4) NOT NULL default '0',
    PRIMARY KEY  (id_tag)
) ENGINE=MyISAM;

CREATE TABLE {db_prefix}tags_log (
    id int(11) NOT NULL auto_increment,
    id_tag mediumint(8) unsigned NOT NULL default '0',
    id_topic mediumint(8) unsigned NOT NULL default '0',
    id_member mediumint(8) unsigned NOT NULL default '0',
    PRIMARY KEY  (id)
) Engine=MyISAM;

# now the changes to stock smf 2 tables

ALTER TABLE {db_prefix}messages ADD has_img tinyint(2) NOT NULL default '0';

ALTER TABLE {db_prefix}members ADD likes_received int(4) unsigned NOT NULL default '0';
ALTER TABLE {db_prefix}members ADD likes_given int(4) unsigned NOT NULL default '0';

ALTER TABLE {db_prefix}boards ADD allow_topics tinyint(4) unsigned NOT NULL default '1';
ALTER TABLE {db_prefix}boards ADD automerge tinyint(4) unsigned NOT NULL default '0';

ALTER TABLE {db_prefix}topics ADD id_prefix smallint(5) unsigned NOT NULL default '0';
ALTER TABLE {db_prefix}topics ADD id_layout tinyint(2) NOT NULL default '0';


