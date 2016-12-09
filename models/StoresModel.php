<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2016 numero2 - Agentur für Internetdienstleistungen
 */



/**
 * Namespace
 */
namespace numero2\StoreLocator;


class StoresModel extends \Model {


	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_storelocator_stores';


	/**
	 * return a list of stores near the given location
	 *
	 * @param  integer $latitude
	 * @param  integer $longitude
	 * @param  integer $distance
	 * @param  array   $categories
	 * @param  string  $country
	 * @param  integer $limit
	 *
	 * @return [type]
	 */

	public static function searchNearby($latitude=NULL, $longitude=NULL, $distance=0, $limit=0, $categories=NULL, $country=NULL) {

		$country = strtoupper($country);

		$objStores = \Database::getInstance()->prepare("
			SELECT
				*
			, 3956 * 1.6 * 2 * ASIN(SQRT( POWER(SIN((? -abs(latitude)) * pi()/180 / 2),2) + COS(? * pi()/180 ) * COS( abs(latitude) *  pi()/180) * POWER(SIN((?-longitude) *  pi()/180 / 2), 2) )) AS distance
			FROM tl_storelocator_stores
			WHERE
					pid IN(".implode(',',$categories).")
				AND latitude != ''
				AND longitude != ''
				".(($country) ? "AND country = {$country} ": '')."
				".(($distance>0) ? "HAVING distance < {$distance} ": '')."
			ORDER BY highlight DESC, distance ASC
			".(($limit>0) ? "LIMIT {$limit} ": '')."
		")->execute(
			$latitude
		,	$latitude
		,	$longitude
		);

		return $objStores;
	}
}
