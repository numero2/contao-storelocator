<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2025, numero2 - Agentur für digitales Marketing GbR
 */


use Contao\Config;
use Geocoder\Provider\BingMaps\BingMaps;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\Here\Here;
use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Provider\OpenCage\OpenCage;
use numero2\StoreLocator\CategoriesModel;
use numero2\StoreLocator\ModuleStoreLocatorDetails;
use numero2\StoreLocator\ModuleStoreLocatorFilter;
use numero2\StoreLocator\ModuleStoreLocatorList;
use numero2\StoreLocator\ModuleStoreLocatorSearch;
use numero2\StoreLocator\ModuleStoreLocatorStaticMap;
use numero2\StoreLocator\OpeningTimes;
use numero2\StoreLocator\StoreLocator;
use numero2\StoreLocator\StoreLocatorBackend;
use numero2\StoreLocator\StoresModel;
use numero2\StoreLocatorBundle\Controller\StoreLocatorImportController;


/**
 * MODELS
 */
$GLOBALS['TL_MODELS'][CategoriesModel::getTable()] = CategoriesModel::class;
$GLOBALS['TL_MODELS'][StoresModel::getTable()] = StoresModel::class;


/**
 * BACK END MODULES
 */
$GLOBALS['BE_MOD']['content']['storelocator'] = [
    'tables'            => ['tl_storelocator_categories', 'tl_storelocator_stores', 'tl_content']
,   'importStores'      => [StoreLocatorImportController::class, 'importStoreAction']
,   'fillCoordinates'   => [StoreLocatorBackend::class, 'fillCoordinates']
];


/**
 * BACK END FORM FIELDS
 */
$GLOBALS['BE_FFL']['openingTimes'] = OpeningTimes::class;


/**
 * FRONT END MODULES
 */
$GLOBALS['FE_MOD']['storelocator'] = [
    'storelocator_search'       => ModuleStoreLocatorSearch::class
,   'storelocator_list'         => ModuleStoreLocatorList::class
,   'storelocator_filter'       => ModuleStoreLocatorFilter::class
,   'storelocator_details'      => ModuleStoreLocatorDetails::class
,   'storelocator_static_map'   => ModuleStoreLocatorStaticMap::class
];


$GLOBALS['TL_AUTO_ITEM'][] = 'store';


/**
 * REGISTER HOOKS
 */
$GLOBALS['N2SL_HOOKS'] = [
    'modifyListEntries' => []
,   'parseStoreData' => []
];
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = [StoreLocator::class, 'replaceInsertTags'];


/**
 * GEOCODER PROVIDERS
 */
$GLOBALS['N2SL']['geocoder_providers'] = [
    'google-maps' => [
        'class' => GoogleMaps::class
    ,   'init_callback' => function($httpClient) {
            if( !Config::get('google_maps_server_key') ) {
                return null;
            }
            return new GoogleMaps($httpClient, null, Config::get('google_maps_server_key'));
        }
    ]
,   'bing-map' => [
        'class' => BingMaps::class
    ,   'init_callback' => function($httpClient) {
            if( !Config::get('bing_map_server_key') ) {
                return null;
            }
            return new BingMaps($httpClient, Config::get('bing_map_server_key'));
        }
    ]
,   'here' => [
        'class' => Here::class
    ,   'init_callback' => function($httpClient) {
            if( !Config::get('here_server_key') ) {
                return null;
            }
            return Here::createUsingApiKey($httpClient, Config::get('here_server_key'));
        }
    ]
,   'nominatim' => [
        'class' => Nominatim::class
    ,   'init_callback' => function($httpClient) {
            if( !Config::get('nominatim_user_agent') ) {
                return null;
            }
            if( Config::get('nominatim_server') ) {
                return new Nominatim($httpClient, Config::get('nominatim_server'), Config::get('nominatim_user_agent'));
            }
            return Nominatim::withOpenStreetMapServer($httpClient, Config::get('nominatim_user_agent'));
        }
    ]
,   'opencage' => [
        'class' => OpenCage::class
    ,   'init_callback' => function($httpClient) {
            if( !Config::get('opencage_api_key') ) {
                return null;
            }
            return new OpenCage($httpClient, Config::get('opencage_api_key'));
        }
    ]
];

$GLOBALS['N2SL']['javascript_providers'] = [
    'google-maps' => [
        'init_callback' => function() {
            if( !Config::get('google_maps_browser_key') ) {
                return false;
            }
            return true;
        }
    ]
,   'leaflet' => [
        'init_callback' => function() {
            return true;
        }
    ]
];