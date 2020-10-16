<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2019 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2020 numero2 - Agentur für digitales Marketing GbR
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
     * Return a list of stores near the given location, results can be filtered by distance, number and categories
     *
     * @param  integer $latitude
     * @param  integer $longitude
     * @param  integer $distance
     * @param  integer $limit
     * @param  array   $categories
     *
     * @return collection
     */
    public static function searchNearby($latitude=NULL, $longitude=NULL, $distance=0, $limit=0, $categories=NULL, $filter=NULL, $order=NULL) {

        $objStores = \Database::getInstance()->prepare("
            SELECT
                *
            , 3956 * 1.6 * 2 * ASIN(SQRT( POWER(SIN((? -abs(latitude)) * pi()/180 / 2),2) + COS(? * pi()/180 ) * COS( abs(latitude) *  pi()/180) * POWER(SIN((?-longitude) *  pi()/180 / 2), 2) )) AS distance
            FROM ".self::$strTable."
            WHERE
                published='1'
				AND pid IN(".implode(',',$categories).")
                AND latitude != ''
                AND longitude != ''
                ".($filter? "AND ".$filter:"")."
                ".(($distance>0) ? "HAVING distance < {$distance} ": '')."
            ORDER BY ".($order?$order.", ":"")."distance ASC, highlight DESC
            ".(($limit>0) ? "LIMIT {$limit} ": '')."
        ")->execute(
            $latitude
        ,   $latitude
        ,   $longitude
        );

        return self::createCollectionFromDbResult($objStores,self::$strTable);
    }


    /**
     * Return a list of stores in the given country location, results can be filtered by number and categories
     *
     * @param  string  $country
     * @param  integer $limit
     * @param  array   $categories
     *
     * @return collection
     */
    public static function searchCountry($country=NULL, $limit=0, $categories=NULL, $filter=NULL, $order=NULL ) {

        $objStores = \Database::getInstance()->prepare("
            SELECT
                *
            FROM ".self::$strTable."
            WHERE
                published='1'
				AND pid IN(".implode(',',$categories).")
                ".(($country) ? "AND country = '{$country}' ": '')."
                ".($filter? "AND ".$filter:"")."
            ORDER BY ".($order?$order.", ":"")."highlight DESC
            ".(($limit>0) ? "LIMIT {$limit} ": '')."
        ")->execute();

        return self::createCollectionFromDbResult($objStores,self::$strTable);
    }


    /**
     * Return a list of stores in the given geocoordinates, results can be filtered by categories
     *
     * @param  integer $formLng
     * @param  integer $toLng
     * @param  integer $formLat
     * @param  integer  $toLat
     * @param  array   $categories
     * @param  integer $limit
     * @param  String $filter
     *
     * @return collection
     */
    public static function searchBetweenCoords($formLng=NULL, $toLng=NULL, $formLat=NULL, $toLat=NULL, $categories=NULL, $limit=0, $filter=NULL ) {

        $objStores = \Database::getInstance()->prepare("
            SELECT
                *
            FROM tl_storelocator_stores
            WHERE
                published='1'
				AND ? < longitude AND longitude < ?
            AND ? < latitude AND latitude < ?
            ".($categories? "AND pid IN(".implode(',',$categories).")":"")."
            ".($filter? "AND ".$filter:"")."
            ".(($limit>0) ? "LIMIT {$limit} ": 'LIMIT 500')."
        ")->execute(floatval($formLng), floatval($toLng), floatval($formLat), floatval($toLat));

        return self::createCollectionFromDbResult($objStores,self::$strTable);
    }
}
