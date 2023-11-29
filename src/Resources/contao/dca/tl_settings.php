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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use numero2\StoreLocator\Geocoder;
use numero2\StoreLocator\StoreLocatorBackend;


/**
 * Add config to tl_settings
 */
if( empty($GLOBALS['TL_DCA']['tl_settings']['config']['onload_callback'][0]) ) {
    $GLOBALS['TL_DCA']['tl_settings']['config']['onload_callback'] = [[StoreLocatorBackend::class, 'showNoProviderAvailable']];
} else {
    $GLOBALS['TL_DCA']['tl_settings']['config']['onload_callback'][] = [StoreLocatorBackend::class, 'showNoProviderAvailable'];
}


/**
 * Add palettes to tl_settings
 */
$pm = PaletteManipulator::create()
    ->addLegend('storelocator_legend', 'timeout_legend', 'before')
;

// general fields
$pm->addField(['sl_provider_backend'], 'storelocator_legend', 'append');

// google maps
if( Geocoder::getInstance()->hasProvider('google-maps') ) {
    $pm->addField(['google_maps_server_key'], 'storelocator_legend', 'append');
}
$pm->addField(['google_maps_browser_key'], 'storelocator_legend', 'append');

// bing map
if( Geocoder::getInstance()->hasProvider('bing-map') ) {
    $pm->addField(['bing_map_server_key'], 'storelocator_legend', 'append');
}

// here
if( Geocoder::getInstance()->hasProvider('here') ) {
    $pm->addField(['here_server_key'], 'storelocator_legend', 'append');
}

// nominatim
if( Geocoder::getInstance()->hasProvider('nominatim') ) {
    $pm->addField(['nominatim_server', 'nominatim_user_agent'], 'storelocator_legend', 'append');
}

// opencage
if( Geocoder::getInstance()->hasProvider('opencage') ) {
    $pm->addField(['opencage_api_key'], 'storelocator_legend', 'append');
}

$pm->applyToPalette('default', 'tl_settings');


/**
 * Add fields to tl_settings
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['sl_provider_backend'] = [
    'inputType'         => 'select'
,   'options'           => ['hide', 'google-maps', 'bing-map', 'here']
,   'reference'         => &$GLOBALS['TL_LANG']['tl_settings']['sl_provider_backend_options']
,   'eval'              => ['includeBlankOption'=>true, 'tl_class'=>'clr w50']
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['google_maps_server_key'] = [
    'inputType'         => 'text'
,   'eval'              => ['tl_class'=>'clr w50']
];
$GLOBALS['TL_DCA']['tl_settings']['fields']['google_maps_browser_key'] = [
    'inputType'         => 'text'
,   'eval'              => ['tl_class'=>'w50']
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['bing_map_server_key'] = [
    'inputType'         => 'text'
,   'eval'              => ['tl_class'=>'clr w50']
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['here_server_key'] = [
    'inputType'         => 'text'
,   'eval'              => ['tl_class'=>'clr w50']
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['nominatim_server'] = [
    'inputType'         => 'text'
,   'eval'              => ['tl_class'=>'clr w50']
];
$GLOBALS['TL_DCA']['tl_settings']['fields']['nominatim_user_agent'] = [
    'inputType'         => 'text'
,   'eval'              => ['tl_class'=>'w50']
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['opencage_api_key'] = [
    'inputType'         => 'text'
,   'eval'              => ['tl_class'=>'clr w50']
];