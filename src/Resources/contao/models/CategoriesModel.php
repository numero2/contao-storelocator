<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2021 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2021 numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocator;

use Contao\Database;
use Contao\Model;


class CategoriesModel extends Model {


    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_storelocator_categories';


    /**
     * Returns a list containing all store categories
     *
     * @return Contao\Collection|numero2\StoreLocator\CategoriesModel|null The model or null if there are no categories
     */
    public static function getCategories() {

        $objResult = null;

        $objResult = Database::getInstance()->prepare("
            SELECT
                id,
                title
            FROM ".self::$strTable."
        ")->execute();

        return self::createCollectionFromDbResult($objResult, self::$strTable);
    }


    /**
     * Returns a list of all map pins used and their category
     *
     * @return Contao\Collection|numero2\StoreLocator\CategoriesModel|null The model or null if there are no categories
     */
    public static function getMapPins() {

        $objResult = null;

        $objResult = Database::getInstance()->prepare("
            SELECT
                id,
                alias,
                map_pin
            FROM ".self::$strTable."
        ")->execute();

        return self::createCollectionFromDbResult($objResult, self::$strTable);
    }
}
