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
 * MODELS
 */
$GLOBALS['TL_MODELS'][\numero2\StoreLocator\StoresModel::getTable()] = 'numero2\StoreLocator\StoresModel';
$GLOBALS['TL_MODELS'][\numero2\StoreLocator\CategoriesModel::getTable()] = 'numero2\StoreLocator\CategoriesModel';


/**
 * BACK END MODULES
 */
$GLOBALS['BE_MOD']['content']['storelocator'] = array(
    'tables'            => array('tl_storelocator_categories', 'tl_storelocator_stores')
,   'icon'              => 'system/modules/storelocator/assets/icon.png'
,   'stylesheet'        => 'system/modules/storelocator/assets/backend.css'
,   'importStores'      => array( '\numero2\StoreLocator\ModuleStoreLocatorImporter', 'showImport' )
,   'fillCoordinates'   => array( '\numero2\StoreLocator\StoreLocatorBackend', 'fillCoordinates' )
);


/**
 * BACK END FORM FIELDS
 */
$GLOBALS['BE_FFL']['openingTimes'] = '\numero2\StoreLocator\OpeningTimes';


/**
 * FRONT END MODULES
 */
$GLOBALS['FE_MOD']['storelocator'] = array(
    'storelocator_search'   => '\numero2\StoreLocator\ModuleStoreLocatorSearch'
,   'storelocator_list'     => '\numero2\StoreLocator\ModuleStoreLocatorList'
,   'storelocator_details'  => '\numero2\StoreLocator\ModuleStoreLocatorDetails'
);


/**
 * REGISTER HOOKS
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('\numero2\StoreLocator\StoreLocator', 'replaceInsertTags');
