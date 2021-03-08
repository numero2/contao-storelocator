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


/**
 * Table tl_storelocator_stores
 */
$GLOBALS['TL_DCA']['tl_storelocator_stores'] = [

    'config' => [
        'dataContainer'               => 'Table'
    ,   'ptable'                      => 'tl_storelocator_categories'
    ,   'onsubmit_callback'           => [['\numero2\StoreLocator\StoreLocatorBackend', 'fillCoordinates']]
    ,   'onload_callback'             => [['\numero2\StoreLocator\StoreLocatorBackend','showGoogleKeysMissingMessage']]
    ,   'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ]
,   'list' => [
        'sorting' => [
            'mode'                    => 4
        ,   'fields'                  => ['city']
        ,   'flag'                    => 1
        ,   'headerFields'            => ['title']
        ,   'panelLayout'             => 'search,limit'
        ,   'child_record_callback'   => ['\numero2\StoreLocator\DCAHelper\Stores', 'listStores']
        ]
    ,   'global_operations' => [
            'all' => [
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all']
            ,   'href'                => 'act=select'
            ,   'class'               => 'header_edit_all'
            ,   'attributes'          => 'onclick="Backend.getScrollOffset();"'
            ]
        ,   'fillCoordinates' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['fillCoordinates']
            ,   'href'                => 'key=fillCoordinates'
            ,   'class'               => 'header_fill_coordinates'
            ,   'attributes'          => 'onclick="Backend.getScrollOffset(); AjaxRequest.displayBox(\''.$GLOBALS['TL_LANG']['tl_storelocator_stores']['ajax_coordinates_running'].'\');"'
            ]
        ,   'importStores' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['importStores']
            ,   'href'                => 'key=importStores'
            ,   'class'               => 'header_stores_import'
            ,   'attributes'          => 'onclick="Backend.getScrollOffset()"'
            ]
        ]
    ,   'operations' => [
            'edit' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['edit']
            ,   'href'                => 'act=edit'
            ,   'icon'                => 'edit.svg'
            ]
        ,   'copy' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['copy']
            ,   'href'                => 'act=copy'
            ,   'icon'                => 'copy.svg'
            ]
        ,   'delete' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['delete']
            ,   'href'                => 'act=delete'
            ,   'icon'                => 'delete.svg'
            ,   'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ]
        ,   'toggle' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['publish']
            ,   'icon'                => 'visible.svg'
            ,   'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"'
            ,   'button_callback'     => ['\numero2\StoreLocator\DCAHelper\Stores', 'toggleIcon']
            ]
        ,   'highlight' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['highlight']
            ,   'icon'                => 'featured.svg'
            ,   'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleFeatured(this,%s)"'
            ,   'button_callback'     => ['\numero2\StoreLocator\DCAHelper\Stores', 'iconHighlight']
            ]
        ,   'coords' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['coords']
            ,   'href'                => 'act=show'
            ,   'icon'                => ['bundles/storelocator/coords0.svg', 'bundles/storelocator/coords1.svg']
            ,   'button_callback'     => ['\numero2\StoreLocator\DCAHelper\Stores', 'coordsButton']
            ]
        ]
    ]
,   'palettes' => [
        'default'                     => '{common_legend},name,alias,email,url,phone,fax,description,singleSRC;{adress_legend},street,postal,city,country;{times_legend},opening_times;{geo_legend},geo_explain,map,longitude,latitude;{publish_legend},highlight,published;'
    ]
,   'fields' => [
        'id' => [
            'sql'           => "int(10) unsigned NOT NULL auto_increment"
        ]
    ,   'pid' => [
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        ]
    ,   'tstamp' => [
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        ]
    ,   'name' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['name']
        ,   'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'alias' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['alias']
        ,   'exclude'           => true
        ,   'inputType'         => 'text'
        ,   'eval'              => ['rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50']
        ,   'save_callback'     => [['\numero2\StoreLocator\DCAHelper\Stores', 'generateAlias']]
        ,   'sql'               => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
        ]
    ,   'email' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['email']
        ,   'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['rgxp'=>'email ', 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'url' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['url']
        ,   'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['rgxp'=>'url ', 'maxlength'=>255, 'tl_class'=>'w50']
        ,   'save_callback'     => [['\numero2\StoreLocator\DCAHelper\Stores', 'checkURL']]
        ,   'sql'               => "varchar(255) NOT NULL default ''"
        ]
    ,   'phone' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['phone']
        ,   'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['rgxp'=>'phone ', 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'fax' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['fax']
        ,   'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['rgxp'=>'phone ', 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'description' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['description']
        ,   'inputType'         => 'textarea'
        ,   'eval'              => ['rte'=>'tinyMCE', 'tl_class'=>'clr']
        ,   'sql'               => "text NULL"
        ]
    ,   'singleSRC' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['singleSRC']
        ,   'inputType'         => 'fileTree'
        ,   'eval'              => ['filesOnly'=>true, 'extensions'=>\Config::get('validImageTypes'), 'fieldType'=>'radio']
        ,   'sql'               => "binary(16) NULL"
        ]
    ,   'street' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['street']
        ,   'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'postal' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['postal']
        ,   'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'city' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['city']
        ,   'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'country' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['country']
        ,   'inputType'         => 'select'
        ,   'options_callback'  => ['\numero2\StoreLocator\DCAHelper\Stores', 'getCountries']
        ,   'default'           => 'de'
        ,   'search'            => true
        ,   'eval'              => ['mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50', 'chosen'=>true]
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'opening_times' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['opening_times']
        ,   'exclude'           => true
        ,   'inputType'         => 'openingTimes'
        ,   'sql'               => "text NULL"
        ]
    ,   'longitude' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['longitude']
        ,   'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'latitude' => [
            'label'             => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['latitude']
        ,   'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'map' => [
            'label'                => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['latitude']
        ,   'input_field_callback' => ['\numero2\StoreLocator\DCAHelper\Stores', 'showMap']
        ]
    ,   'geo_explain' => [
            'label'                => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['latitude']
        ,   'input_field_callback' => ['\numero2\StoreLocator\DCAHelper\Stores', 'showGeoExplain']
        ]
    ,   'file' => [
            'label'                => &$GLOBALS['TL_LANG']['tl_storelocator']['import']['file']
        ,   'eval'                 => ['fieldType'=>'radio', 'files'=>true, 'filesOnly'=>true, 'extensions'=>'csv', 'class'=>'mandatory']
        ]
    ,   'highlight' => [
            'label'                => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['highlight']
        ,   'inputType'            => 'checkbox'
        ,   'search'               => true
        ,   'eval'                 => ['mandatory'=>false, 'tl_class'=>'w50']
        ,   'sql'                  => "char(1) NOT NULL default '0'"
        ]
    ,   'published' => [
            'label'                => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['publish']
        ,   'inputType'            => 'checkbox'
        ,   'eval'                 => ['doNotCopy'=>true]
        ,   'sql'                  => "char(1) NOT NULL default ''"
        ]
    ]
];
