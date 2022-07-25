<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2022 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2022 numero2 - Agentur für digitales Marketing GbR
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
if( !array_key_exists('stylesheet', $GLOBALS['BE_MOD']['design']['themes']) ) {
    $GLOBALS['BE_MOD']['design']['themes']['stylesheet'] = [];
}
$GLOBALS['BE_MOD']['design']['themes']['stylesheet'] = (array) $GLOBALS['BE_MOD']['design']['themes']['stylesheet'];
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


$GLOBALS['TL_AUTO_ITEM'][] = 'store';


/**
 * REGISTER HOOKS
 */
$GLOBALS['N2SL_HOOKS'] = [
    'modifyListEntries' => []
,   'parseStoreData' => []
];
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['\numero2\StoreLocator\StoreLocator', 'replaceInsertTags'];


/**
 * GEOCODER PROVIDERS
 */
$GLOBALS['N2SL']['geocoder_providers'] = [
    'google-maps' => [
        'class' => '\Geocoder\Provider\GoogleMaps\GoogleMaps'
    ,   'init_callback' => function($httpClient) {
            if( !Contao\Config::get('google_maps_server_key') ) {
                return null;
            }
            return new \Geocoder\Provider\GoogleMaps\GoogleMaps($httpClient, null, Contao\Config::get('google_maps_server_key'));
        }
    ]
,   'bing-map' => [
        'class' => '\Geocoder\Provider\BingMaps\BingMaps'
    ,   'init_callback' => function($httpClient) {
            if( !Contao\Config::get('bing_map_server_key') ) {
                return null;
            }
            return new \Geocoder\Provider\BingMaps\BingMaps($httpClient, Contao\Config::get('bing_map_server_key'));
        }
    ]
,   'here' => [
        'class' => '\Geocoder\Provider\Here\Here'
    ,   'init_callback' => function($httpClient) {
            if( !Contao\Config::get('here_server_key') ) {
                return null;
            }
            return \Geocoder\Provider\Here\Here::createUsingApiKey($httpClient, Contao\Config::get('here_server_key'));
        }
    ]
,   'nominatim' => [
        'class' => '\Geocoder\Provider\Nominatim\Nominatim'
    ,   'init_callback' => function($httpClient) {
            if( !Contao\Config::get('nominatim_user_agent') ) {
                return null;
            }
            if( !Contao\Config::get('nominatim_server') ) {
                return new \Geocoder\Provider\Nominatim($httpClient, Contao\Config::get('nominatim_server'), Contao\Config::get('nominatim_user_agent'));
            }
            return \Geocoder\Provider\Nominatim\Nominatim::withOpenStreetMapServer($httpClient, Contao\Config::get('nominatim_user_agent'));
        }
    ]
,   'opencage' => [
        'class' => '\Geocoder\Provider\OpenCage\OpenCage'
    ,   'init_callback' => function($httpClient) {
            if( !Contao\Config::get('opencage_api_key') ) {
                return null;
            }
            return new \Geocoder\Provider\OpenCage\OpenCage($httpClient, Contao\Config::get('opencage_api_key'));
        }
    ]
];

$GLOBALS['N2SL']['javascript_providers'] = [
    'google-maps' => [
        'init_callback' => function() {
            if( !Contao\Config::get('google_maps_browser_key') ) {
                return false;
            }
            return true;
        }
    ]
];
