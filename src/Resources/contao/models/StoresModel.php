<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2023, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator;

use Contao\Database;
use Contao\Model;


class StoresModel extends Model {


    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_storelocator_stores';


    /**
     * Return a list of stores near the given location, results can be filtered by distance, number and categories
     *
     * @param integer $latitude
     * @param integer $longitude
     * @param integer $distance
     * @param integer $limit
     * @param array $categories
     * @param string $filter
     * @param string $order
     *
     * @return Contao\Collection|numero2\StoreLocator\StoresModel|null
     */
    public static function searchNearby( $latitude, $longitude, $distance=0, $limit=0, ?array $categories=null, ?string $filter=null, ?string $order=null ) {

        $objStores = Database::getInstance()->prepare("
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

        return self::createCollectionFromDbResult($objStores, self::$strTable);
    }


    /**
     * Return a list of stores in the given country location, results can be filtered by number and categories
     *
     * @param string $country
     * @param integer $limit
     * @param array $categories
     * @param string $filter
     * @param string $order
     *
     * @return Contao\Collection|numero2\StoreLocator\StoresModel|null
     */
    public static function searchCountry( string $country, $limit=0, ?array $categories=null, ?string $filter=null, ?string $order=null ) {

        $objStores = Database::getInstance()->prepare("
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

        return self::createCollectionFromDbResult($objStores, self::$strTable);
    }


    /**
     * Return a list of stores in the given geocoordinates, results can be filtered by categories
     *
     * @param integer $fromLng
     * @param integer $toLng
     * @param integer $fromLat
     * @param integer $toLat
     * @param integer $limit
     * @param array $categories
     * @param string $filter
     *
     * @return Contao\Collection|numero2\StoreLocator\StoresModel|null
     */
    public static function searchBetweenCoords( $fromLng, $toLng, $fromLat, $toLat, $limit=0, ?array $categories=null, ?string $filter=null ) {

        $objStores = Database::getInstance()->prepare("
            SELECT
                *
            FROM tl_storelocator_stores
            WHERE
                published='1'
                AND latitude != ''
                AND longitude != ''
                AND ? < CAST(longitude AS DECIMAL(10,6)) AND CAST(longitude AS DECIMAL(10,6)) < ?
                AND ? < CAST(latitude AS DECIMAL(10,6)) AND CAST(latitude AS DECIMAL(10,6)) < ?
                ".($categories? "AND pid IN(".implode(',',$categories).")":"")."
                ".($filter? "AND ".$filter:"")."
            ".(($limit>0) ? "LIMIT {$limit} ": 'LIMIT 500')."
        ")->execute(floatval($fromLng), floatval($toLng), floatval($fromLat), floatval($toLat));

        return self::createCollectionFromDbResult($objStores, self::$strTable);
    }
}
