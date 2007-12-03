CREATE TABLE `xrowevent_event` (
  `start_date` int(11) default NULL,
  `end_date` int(11) default NULL,
  `max_participants` int(11) default NULL,
  `status` int(11) default NULL,
  `contentobject_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`contentobject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `xrowevent_participants` (
  `event_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `created` int(11) default NULL,
  PRIMARY KEY  (`event_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `xrowevent_persons` (
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`event_id`,`user_id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# update
ALTER TABLE xrowevent_event ADD COLUMN `comment` INTEGER NOT NULL DEFAULT 0 AFTER `contentobject_id`;
ALTER TABLE xrowevent_participants ADD COLUMN `comment` TEXT AFTER `created`;