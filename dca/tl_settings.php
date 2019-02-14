<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2019 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2019 numero2 - Agentur für digitales Marketing
 */


/**
 * Add config to tl_settings
 */
if( empty($GLOBALS['TL_DCA']['tl_settings']['config']['onload_callback'][0])){
    $GLOBALS['TL_DCA']['tl_settings']['config']['onload_callback'] = array( array('numero2\StoreLocator\StoreLocatorBackend','showGoogleKeysMissingMessage') );
} else {
    $GLOBALS['TL_DCA']['tl_settings']['config']['onload_callback'][] = array('numero2\StoreLocator\StoreLocatorBackend','showGoogleKeysMissingMessage');
}

/**
 * Add palettes to tl_settings
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace(
    ';{timeout_legend'
,   ';{storelocator_legend:hide},google_maps_server_key,google_maps_browser_key;{timeout_legend'
,   $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']
);


/**
 * Add fields to tl_settings
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['google_maps_server_key'] = array(
    'label'             => &$GLOBALS['TL_LANG']['tl_settings']['google_maps_server_key']
,   'inputType'         => 'text'
,   'eval'              => array('mandatory'=>false, 'tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['google_maps_browser_key'] = array(
    'label'             => &$GLOBALS['TL_LANG']['tl_settings']['google_maps_browser_key']
,   'inputType'         => 'text'
,   'eval'              => array('mandatory'=>false, 'tl_class'=>'w50')
);
