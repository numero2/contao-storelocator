<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2023, numero2 - Agentur für digitales Marketing GbR
 */


use Contao\Config;
use Contao\DC_Table;
use numero2\StoreLocator\DCAHelper\Stores;
use numero2\StoreLocator\DCAHelper\Tags;
use numero2\StoreLocator\StoreLocatorBackend;
use numero2\TagsBundle\TagsBundle;
use Contao\CoreBundle\DataContainer\PaletteManipulator;


/**
 * Table tl_storelocator_stores
 */
$GLOBALS['TL_DCA']['tl_storelocator_stores'] = [

    'config' => [
        'dataContainer'               => defined('VERSION') ? 'Table' : DC_Table::class
    ,   'ptable'                      => 'tl_storelocator_categories'
    ,   'onsubmit_callback'           => [[StoreLocatorBackend::class, 'fillCoordinates']]
    ,   'onload_callback'             => [[StoreLocatorBackend::class, 'showNoProviderAvailable']]
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
        ,   'flag'                    => 11
        ,   'headerFields'            => ['title']
        ,   'panelLayout'             => 'filter;sort,search,limit'
        ,   'child_record_callback'   => [Stores::class, 'listStores']
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
            ,   'attributes'          => 'onclick="Backend.getScrollOffset(); AjaxRequest.displayBox(\'' . ($GLOBALS['TL_LANG']['tl_storelocator_stores']['ajax_coordinates_running'] ?? null) . '\');"'
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
            ,   'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ]
        ,   'toggle' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['published']
            ,   'icon'                => 'visible.svg'
            ,   'button_callback'     => [Stores::class, 'toggleIcon']
            ]
        ,   'highlight' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['highlight']
            ,   'icon'                => 'featured.svg'
            ,   'attributes'          => 'onclick="Backend.getScrollOffset();"'
            ,   'button_callback'     => [Stores::class, 'iconHighlight']
            ]
        ,   'coords' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_stores']['coords']
            ,   'href'                => 'act=show'
            ,   'icon'                => ['bundles/storelocator/coords0.svg', 'bundles/storelocator/coords1.svg']
            ,   'button_callback'     => [Stores::class, 'coordsButton']
            ]
        ]
    ]
,   'palettes' => [
        'default'           => '{common_legend},name,alias,singleSRC,description;{contact_legend},email,url,phone,fax;{adress_legend},street,postal,city,country;{times_legend},opening_times;{geo_legend},geo_explain,map,longitude,latitude;{publish_legend},published,highlight;'
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
            'inputType'         => 'text'
        ,   'search'            => true
        ,   'feSortable'        => true
        ,   'sorting'           => true
        ,   'eval'              => ['mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'alias' => [
            'exclude'           => true
        ,   'inputType'         => 'text'
        ,   'eval'              => ['rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50']
        ,   'save_callback'     => [[Stores::class, 'generateAlias']]
        ,   'sql'               => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
        ]
    ,   'email' => [
            'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['rgxp'=>'email ', 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'url' => [
            'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['rgxp'=>'url ', 'maxlength'=>255, 'tl_class'=>'w50', 'placeholder'=>'https://example.com']
        ,   'save_callback'     => [[Stores::class, 'checkURL']]
        ,   'sql'               => "varchar(255) NOT NULL default ''"
        ]
    ,   'phone' => [
            'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['rgxp'=>'phone', 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'fax' => [
            'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['rgxp'=>'phone', 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'description' => [
            'inputType'         => 'textarea'
        ,   'eval'              => ['rte'=>'tinyMCE', 'tl_class'=>'clr']
        ,   'sql'               => "text NULL"
        ]
    ,   'singleSRC' => [
            'inputType'         => 'fileTree'
        ,   'eval'              => ['filesOnly'=>true, 'extensions'=>Config::get('validImageTypes'), 'fieldType'=>'radio', 'tl_class'=>'clr']
        ,   'sql'               => "binary(16) NULL"
        ]
    ,   'street' => [
            'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'postal' => [
            'inputType'         => 'text'
        ,   'search'            => true
        ,   'feSortable'        => true
        ,   'filter'            => true
        ,   'eval'              => ['mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'city' => [
            'inputType'         => 'text'
        ,   'search'            => true
        ,   'feSortable'        => true
        ,   'filter'            => true
        ,   'sorting'           => true
        ,   'eval'              => ['mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'country' => [
            'inputType'         => 'select'
        ,   'search'            => true
        ,   'feSortable'        => true
        ,   'filter'            => true
        ,   'sorting'           => true
        ,   'options_callback'  => [Stores::class, 'getCountries']
        ,   'default'           => 'de'
        ,   'eval'              => ['mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50', 'chosen'=>true]
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'opening_times' => [
            'exclude'           => true
        ,   'inputType'         => 'openingTimes'
        ,   'sql'               => "text NULL"
        ]
    ,   'longitude' => [
            'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'latitude' => [
            'inputType'         => 'text'
        ,   'search'            => true
        ,   'eval'              => ['mandatory'=>false, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'               => "varchar(64) NOT NULL default ''"
        ]
    ,   'map' => [
            'input_field_callback' => [Stores::class, 'showMap']
        ]
    ,   'geo_explain' => [
            'input_field_callback' => [Stores::class, 'showGeoExplain']
        ]
    ,   'highlight' => [
            'inputType'            => 'checkbox'
        ,   'filter'               => true
        ,   'eval'                 => ['doNotCopy'=>true, 'tl_class'=>'w50']
        ,   'sql'                  => "char(1) NOT NULL default ''"
        ]
    ,   'published' => [
            'inputType'            => 'checkbox'
        ,   'toggle'               => true
        ,   'filter'               => true
        ,   'eval'                 => ['doNotCopy'=>true, 'tl_class'=>'w50']
        ,   'sql'                  => "char(1) NOT NULL default ''"
        ]
    ]
];


if( class_exists(TagsBundle::class) ) {

    PaletteManipulator::create()
        ->addLegend('tags_legend', 'common_legend', 'after')
        ->addField('tags', 'tags_legend', 'append')
        ->applyToPalette('default', 'tl_storelocator_stores')
    ;

    $GLOBALS['TL_DCA']['tl_storelocator_stores']['fields']['tags'] = [
        'exclude'           => true
    ,   'inputType'         => 'select'
    ,   'filter'            => true
    ,   'foreignKey'        => 'tl_tags.tag'
    ,   'options_callback'  => ['numero2_tags.listener.data_container.tags', 'getTagOptions']
    ,   'save_callback'     => [['numero2_tags.listener.data_container.tags', 'saveTags']]
    ,   'eval'              => ['multiple'=>true, 'size'=>8, 'tl_class'=>'clr long tags', 'chosen'=>true]
    ,   'sql'               => "blob NULL"
    ,   'relation'          => ['type'=>'hasMany', 'load'=>'eager']
    ];
}