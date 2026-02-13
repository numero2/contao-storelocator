<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2026, numero2 - Agentur für digitales Marketing GbR
 */


use Contao\DataContainer;
use Contao\DC_Table;
use numero2\StoreLocator\DCAHelper\Categories;
use numero2\StoreLocator\StoreLocatorBackend;


$GLOBALS['TL_DCA']['tl_storelocator_categories'] = [

    'config' => [
        'dataContainer'               => DC_Table::class
    ,   'ctable'                      => ['tl_storelocator_stores']
    ,   'switchToEdit'                => true
    ,   'onload_callback'             => [[StoreLocatorBackend::class, 'showNoProviderAvailable']]
    ,   'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ]
,   'list' => [
        'sorting' => [
            'mode'                    => DataContainer::MODE_SORTED
        ,   'fields'                  => ['title']
        ,   'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC
        ,   'panelLayout'             => 'filter;search,limit'
        ]
    ,   'label' => [
            'fields'                  => ['title']
        ,   'format'                  => '%s'
        ]
    ,   'global_operations' => [
            'all'
        ]
    ,   'operations' => [
            'edit'
        ,   'children'
        ,   'copy'
        ,   'delete'
        ,   'show'
        ]
    ]
,   'palettes' => [
        'default'           => '{title_legend},title,alias;{map_legend},map_pin;'
    ]
,   'fields' => [
        'id' => [
            'sql'           => "int(10) unsigned NOT NULL auto_increment"
        ]
    ,   'tstamp' => [
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        ]
    ,   'title' => [
            'inputType'     => 'text'
        ,   'search'        => true
        ,   'eval'          => ['mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50']
        ,   'sql'           => "varchar(64) NOT NULL default ''"
        ]
    ,   'alias' => [
            'exclude'       => true
        ,   'inputType'     => 'text'
        ,   'eval'          => ['rgxp'=>'alias', 'doNotCopy'=>true, 'maxlength'=>128, 'tl_class'=>'w50']
        ,   'save_callback' => [[Categories::class, 'generateAlias']]
        ,   'sql'           => "varchar(255) BINARY NOT NULL default ''"
        ]
    ,   'map_pin' => [
            'inputType'     => 'fileTree'
        ,   'eval'          => ['filesOnly'=>true, 'extensions'=>'%contao.image.valid_extensions%', 'fieldType'=>'radio']
        ,   'sql'           => "binary(16) NULL"
        ]
    ]
];
