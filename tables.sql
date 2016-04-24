set foreign_key_checks = 0;
/* Here we'll remove tables if they exists,
so we won't end up with duplicates in our database */

/*drop table if exists tableName; */
drop table if exists customers;
drop table if exists orders;
set foreign_key_checks = 1;

CREATE TABLE webchat_lines (
  id int(10) unsigned NOT NULL auto_increment,
  author varchar(16) NOT NULL,
  gravatar varchar(32) NOT NULL,
  text varchar(255) NOT NULL,
  ts timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (id),
  KEY ts (ts)
);

-- --------------------------------------------------------

--
-- Table structure for table `webchat_users`
--

CREATE TABLE webchat_users (
  id int(10) unsigned NOT NULL auto_increment,
  name varchar(16) NOT NULL,
  gravatar varchar(32) NOT NULL,
  last_activity timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (id),
  UNIQUE KEY name (name),
  KEY last_activity (last_activity)
);
