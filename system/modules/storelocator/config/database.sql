--
-- Table `tl_storelocator_category`
--

CREATE TABLE `tl_storelocator_category` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `title` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table `tl_storelocator_stores`
--

CREATE TABLE `tl_storelocator_stores` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `email` varchar(64) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `phone` varchar(64) NOT NULL default '',
  `fax` varchar(64) NOT NULL default '',
  `street` varchar(64) NOT NULL default '',
  `postal` varchar(64) NOT NULL default '',
  `city` varchar(64) NOT NULL default '',
  `country` varchar(64) NOT NULL default '',
  `opening_times` text NULL,
  `longitude` varchar(64) NOT NULL default '',
  `latitude` varchar(64) NOT NULL default '',
  `comment` text NULL,
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  UNIQUE KEY `uniq` (`name`, `street`, `postal`, `city`, `country`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `storelocator_search_tpl` varchar(255) NOT NULL default '',
  `storelocator_search_country` varchar(2) NOT NULL default '',
  `storelocator_list_tpl` varchar(255) NOT NULL default '',
  `storelocator_list_categories` text NULL,
  `storelocator_list_limit` varchar(255) NOT NULL default '',
  `storelocator_allow_empty_search` char(1) NOT NULL default '1',
  `storelocator_show_full_country_names` char(1) NOT NULL default '0',
  `storelocator_details_tpl` varchar(255) NOT NULL default '',
  `storelocator_details_maptype` char(10) NOT NULL default 'static',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------