#
# !!! this is for REFERENCE only !!!
# it tracks all the db changes made (new tables and alterations)
# DO NOT run or otherwise use it. ignore it.
#

#
# table to cache preparsed posts
#
CREATE TABLE {$db_prefix}messages_cache (
  id_msg int(10) unsigned NOT NULL default '0',
  body mediumtext NOT NULL,
  style tinyint(3) NOT NULL default '0',
  lang  tinyint(3) NOT NULL default '0',
  updated int(10) NOT NULL default '0',
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
	id_type tinyint(3) NOT NULL default '0',
	desc_id varchar(150) NOT NULL default '',
	formatter varchar(50) NOT NULL default 'act_format_default',
	f_neutral int(8) NOT NULL default '0',
	f_you int(8) NOT NULL default '0',
	f_your int(8) NOT NULL default '0',
	f_you_your int(8) NOT NULL default '0',
	PRIMARY KEY (id_type)
) Engine=MyISAM;
#
# log activities
#
CREATE TABLE {$db_prefix}log_activities (
	id_act int(10) unsigned NOT NULL auto_increment,
	id_member int(10) unsigned NOT NULL default '0',
	updated   int(10) NOT NULL default '0',
	id_type tinyint(3) NOT NULL default '0',
	params varchar(300) NOT NULL default '',
	is_private tinyint(2) NOT NULL default '0',
	id_board smallint(5) NOT NULL default '0',
	id_topic int(10) UNSIGNED NOT NULL default '0',
	id_content int(10) UNSIGNED NOT NULL default '0',
	id_owner int(10) UNSIGNED NOT NULL default '0',
	PRIMARY KEY (id_act),
	KEY (id_member),
	KEY (id_type),
	KEY (updated),
	KEY (id_topic),
	KEY (id_board),
	KEY (id_content)
) Engine=MyISAM;

CREATE TABLE {$db_prefix}log_notifications (
	id_member int(10) unsigned NOT NULL default '0',
	id_act int(10) unsigned NOT NULL default '0',
	unread tinyint(2) NOT NULL default '1',
	PRIMARY KEY(id_member, id_act),
	KEY (unread)
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
	id_owner  int(10) unsigned NOT NULL default '0',
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

#
# Table structure for table `news`
#

CREATE TABLE {$db_prefix}news (
	id_news mediumint(8) unsigned NOT NULL auto_increment,
	body tinytext NOT NULL default '',
	teaser tinytext NOT NULL default '',
	boards varchar(100) NOT NULL default '',
	topics varchar(100) NOT NULL default '',
	groups varchar(50) NOT NULL default '',
	on_index tinyint(2) NOT NULL default '0',
	PRIMARY KEY (id_news)
) ENGINE=MyISAM;

# now the changes to stock smf 2 tables

# this can be used to prevent a post from being cached (unimplemented as of now)
ALTER TABLE {$db_prefix}messages ADD has_img tinyint(2) NOT NULL default '0';

# like stats for members
ALTER TABLE {$db_prefix}members ADD likes_received int(4) unsigned NOT NULL default '0';
ALTER TABLE {$db_prefix}members ADD likes_given int(4) unsigned NOT NULL default '0';
ALTER TABLE {$db_prefix}members DROP website_title;
ALTER TABLE {$db_prefix}members DROP website_url;

# allow topics = 0 - board acts as a pure sub-category and cannot have own topics
ALTER TABLE {$db_prefix}boards ADD allow_topics tinyint(4) unsigned NOT NULL default '1';

# automerge = 1 - multiple posts by the same user at the end of a thread will be automatically
# merged (if time cutoff limit allows it)
ALTER TABLE {$db_prefix}boards ADD automerge tinyint(4) unsigned NOT NULL default '0';

# prefix id for this topic
ALTER TABLE {$db_prefix}topics ADD id_prefix smallint(5) unsigned NOT NULL default '0';

# make the first post of a topic "sticky" on every page and (optionally) give it a different
# postbit layout
# highest bit (0x80) indicates a sticky post, bits 0-7 (id_layout & 0x7f) are the layout id
ALTER TABLE {$db_prefix}topics ADD id_layout tinyint(3) NOT NULL default '0';

# key for the topic prefix (needed for filtering and searching by prefix)
ALTER TABLE {$db_prefix}topics ADD KEY prefix (id_topic, id_prefix),

# description for categories
ALTER TABLE {$db_prefix}categories ADD description varchar(300) NOT NULL default '' AFTER name;

# no longer supported, but might be a good idea to leave in the db...
ALTER TABLE {$db_prefix}members DROP aim;
ALTER TABLE {$db_prefix}members DROP yim;
ALTER TABLE {$db_prefix}members DROP msn;
ALTER TABLE {$db_prefix}members DROP icq;
