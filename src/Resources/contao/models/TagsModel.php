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
use numero2\TagsBundle\TagsRelModel;


class TagsModel extends Model {


    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_tags';


    /**
     * Find all used tags in the given categories
     *
     * @param array $categories
     *
     * @return Collection|TagsModel|null A collection of models or null if there are no tags
     */
    public static function findByStorelocatorCategories( array $categories ) {

        $categories = array_map('\intval', (array)$categories);

        if( empty($categories) ) {
            return null;
        }

        $objResult = Database::getInstance()->prepare("
            SELECT DISTINCT
                t.*
            FROM ".CategoriesModel::getTable()." AS c
                JOIN ".StoresModel::getTable()." AS s ON (s.pid = c.id)
                JOIN ".TagsRelModel::getTable()." AS r ON (r.pid = s.id AND r.ptable = '".StoresModel::getTable()."' AND r.field = 'tags')
                JOIN ".self::getTable()." AS t ON (t.id = r.tag_id)
            WHERE
                c.id in (".implode(',', $categories).")
            ORDER BY t.tag ASC
        ")->execute();

        return static::createCollectionFromDbResult($objResult, self::$strTable);
    }


    /**
     * Count how many times the given tag was used
     *
     * @param int $id
     * @param array $categories
     *
     * @return int
     */
    public static function countByIdAndStorelocatorCategories( $id, array $categories ): int {

        $categories = array_map('\intval', (array)$categories);

        if( empty($categories) ) {
            return 0;
        }

        $objResult = Database::getInstance()->prepare("
            SELECT
                COUNT(*) AS count
            FROM ".CategoriesModel::getTable()." AS c
                JOIN ".StoresModel::getTable()." AS s ON (s.pid = c.id)
                JOIN ".TagsRelModel::getTable()." AS r ON (r.pid = s.id AND r.ptable = '".StoresModel::getTable()."' AND r.field = 'tags')
                JOIN ".self::getTable()." AS t ON (t.id = r.tag_id)
            WHERE
                c.id in (".implode(',', $categories).") AND t.id = ?
        ")->execute( $id );

        return (int)$objResult->count;
    }
}