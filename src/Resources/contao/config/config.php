<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2020 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2020 numero2 - Agentur für digitales Marketing GbR
 */


/**
 * MODELS
 */
$GLOBALS['TL_MODELS'][\numero2\StoreLocator\StoresModel::getTable()] = 'numero2\StoreLocator\StoresModel';
$GLOBALS['TL_MODELS'][\numero2\StoreLocator\CategoriesModel::getTable()] = 'numero2\StoreLocator\CategoriesModel';


/**
 * BACK END MODULES
 */
$GLOBALS['BE_MOD']['content']['storelocator'] = [
    'tables'            => ['tl_storelocator_categories', 'tl_storelocator_stores']
,   'stylesheet'        => 'bundles/storelocator/backend.css'
,   'importStores'      => ['\numero2\StoreLocator\ModuleStoreLocatorImporter', 'showImport']
,   'fillCoordinates'   => ['\numero2\StoreLocator\StoreLocatorBackend', 'fillCoordinates']
];

// Add backend.css to modules
$GLOBALS['BE_MOD']['design']['themes']['stylesheet'] = (array)$GLOBALS['BE_MOD']['design']['themes']['stylesheet'];
$GLOBALS['BE_MOD']['design']['themes']['stylesheet'][] = 'bundles/storelocator/backend.css';


/**
 * BACK END FORM FIELDS
 */
$GLOBALS['BE_FFL']['openingTimes'] = '\numero2\StoreLocator\OpeningTimes';


/**
 * FRONT END MODULES
 */
$GLOBALS['FE_MOD']['storelocator'] = [
    'storelocator_search'       => '\numero2\StoreLocator\ModuleStoreLocatorSearch'
,   'storelocator_list'         => '\numero2\StoreLocator\ModuleStoreLocatorList'
,   'storelocator_filter'       => '\numero2\StoreLocator\ModuleStoreLocatorFilter'
,   'storelocator_details'      => '\numero2\StoreLocator\ModuleStoreLocatorDetails'
,   'storelocator_static_map'   => '\numero2\StoreLocator\ModuleStoreLocatorStaticMap'
];


/**
 * REGISTER HOOKS
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['\numero2\StoreLocator\StoreLocator', 'replaceInsertTags'];
