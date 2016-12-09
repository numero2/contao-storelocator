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


class CategoriesModel extends \Model {


	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_storelocator_categories';


    /**
     * Returns a list containing all store categories
     *
     * @return \StoresModel|null The model or null if there are no categories
     */
	public static function getCategories() {

        $objResult = NULL;

        $objResult = \Database::getInstance()->prepare("
            SELECT
                id,
                title
            FROM ".self::$strTable."
        ")->execute();

        return self::createCollectionFromDbResult($objResult,self::$strTable);
	}
}
