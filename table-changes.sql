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
# id_msg = post (or in the future), content id
# ctype = content type, at the moment ctype = 1 (post) is the only supported type.
#
# id_user - member who gave the like
# id_receiver - member who receives it (= owner of the content)
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
# caches the 4 most recent user_ids / user names for fast retrieval
# of the like status message (e.g. You, foo, bar and xx others like this)
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
# boards = list of boards for which the prefix is allowed
# groups = member groups who are able to use this prefix
# both can be empty in which case a prefix is allowed for all boards / all members
# admin can always use all prefixes anywhere
# a board moderator can use all prefixes for "his" board(s), regardless what groups 
# says.
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

CREATE TABLE {$db_prefix}tags (
    id_tag mediumint(8) NOT NULL auto_increment,
    tag tinytext NOT NULL,
    approved tinyint(4) NOT NULL default '0',
    PRIMARY KEY  (id_tag)
) ENGINE=MyISAM;

CREATE TABLE {$db_prefix}tags_log (
    id int(11) NOT NULL auto_increment,
    id_tag mediumint(8) unsigned NOT NULL default '0',
    id_topic mediumint(8) unsigned NOT NULL default '0',
    id_member mediumint(8) unsigned NOT NULL default '0',
    PRIMARY KEY  (id)
) Engine=MyISAM;

#
# activity types
#
CREATE TABLE {$db_prefix}activity_types (
	id_type tinyint(3) NOT NULL auto_increment,
	desc_id varchar(150) NOT NULL default '',
	formatter varchar(50) NOT NULL default 'act_format_default',
	PRIMARY KEY (id_type)
) Engine=MyISAM;
#
# log activities
#
CREATE TABLE {$db_prefix}log_activities (
	id_member int(10) unsigned NOT NULL default '0',
	updated   int(10) NOT NULL default '0',
	id_type tinyint(3) NOT NULL default '0',
	params varchar(600) NOT NULL default '',
	is_private tinyint(2) NOT NULL default '0',
	id_board smallint(5) NOT NULL default '0',
	KEY (id_member),
	KEY (id_type),
	KEY (updated)
) Engine=MyISAM;


#
# drafts
#
CREATE TABLE {$db_prefix}drafts (
	id_draft int unsigned NOT NULL auto_increment,
	id_member int(10) unsigned NOT NULL default '0',
	id_topic  int(10) unsigned NOT NULL default '0',
	id_board  smallint(5) unsigned NOT NULL default '0',
	id_msg	  int(10) unsigned NOT NULL default '0',
	updated   int(10) NOT NULL default '0',
	icon	  varchar(20) NOT NULL default '',
	smileys   tinyint(2) NOT NULL default '1',
	is_locked tinyint(2) NOT NULL default '0',
	is_sticky tinyint(2) NOT NULL default '0',
	subject varchar(255) NOT NULL default '',
	body mediumtext NOT NULL default '',
	PRIMARY KEY (id_draft),
	KEY (id_member)
) Engine=MyISAM;

# now the changes to stock smf 2 tables

# this can be used to prevent a post from being cached (unimplemented as of now)
ALTER TABLE {$db_prefix}messages ADD has_img tinyint(2) NOT NULL default '0';

# like stats for members
ALTER TABLE {$db_prefix}members ADD likes_received int(4) unsigned NOT NULL default '0';
ALTER TABLE {$db_prefix}members ADD likes_given int(4) unsigned NOT NULL default '0';

# allow topics = 0 - board acts as a pure sub-category and cannot have own topics
ALTER TABLE {$db_prefix}boards ADD allow_topics tinyint(4) unsigned NOT NULL default '1';

# automerge = 1 - multiple posts by the same user at the end of a thread will be automatically
# merged (if time cutoff limit allows it)
ALTER TABLE {$db_prefix}boards ADD automerge tinyint(4) unsigned NOT NULL default '0';

# prefix id for this topic
ALTER TABLE {$db_prefix}topics ADD id_prefix smallint(5) unsigned NOT NULL default '0';

# make the first post of a topic "sticky" on every page and (optionally) give it a different
# postbit layout
ALTER TABLE {$db_prefix}topics ADD id_layout tinyint(2) NOT NULL default '0';


