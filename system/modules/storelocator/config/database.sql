CREATE TABLE `tl_storelocator_stores` (
  UNIQUE KEY `uniq` (`name`, `street`, `postal`, `city`, `country`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;