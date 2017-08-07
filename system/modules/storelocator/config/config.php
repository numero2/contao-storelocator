<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @license   LGPL
 * @copyright 2015 numero2 - Agentur für Internetdienstleistungen
 */


/**
 * BACK END MODULES
 */
$GLOBALS['BE_MOD']['content']['storelocator'] = array(
    'tables'        => array('tl_storelocator_category', 'tl_storelocator_stores')
,   'icon'          => 'system/modules/storelocator/assets/icon.gif'
,   'stylesheet'    => 'system/modules/storelocator/assets/backend.css'
,   'importStores'  => array( '\numero2\StoreLocator\ModuleStoreLocatorImporter', 'showImport' )
);


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
$GLOBALS['TL_HOOKS']['generatePage'][] = array('\numero2\StoreLocator\ModuleStoreLocator', 'addResultsBodyClass');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('\numero2\StoreLocator\ModuleStorelocatorInsertTags', 'replaceInsertTags');