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


use Contao\Config;
use Contao\DC_Table;
use numero2\StoreLocator\DCAHelper\Categories;
use numero2\StoreLocator\StoreLocatorBackend;


/**
 * Table tl_storelocator_categories
 */
$GLOBALS['TL_DCA']['tl_storelocator_categories'] = [

    'config' => [
        'dataContainer'               => defined('VERSION') ? 'Table' : DC_Table::class
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
            'mode'                    => 1
        ,   'fields'                  => ['title']
        ,   'flag'                    => 1
        ,   'panelLayout'             => 'filter;search,limit'
        ]
    ,   'label' => [
            'fields'                  => ['title']
        ,   'format'                  => '%s'
        ]
    ,   'global_operations' => [
            'all' => [
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all']
            ,   'href'                => 'act=select'
            ,   'class'               => 'header_edit_all'
            ,   'attributes'          => 'onclick="Backend.getScrollOffset();"'
            ]
        ]
    ,   'operations' => [
            'edit' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['edit']
            ,   'href'                => 'table=tl_storelocator_stores'
            ,   'icon'                => 'edit.svg'
            ]
        ,   'editheader' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['editheader']
            ,   'href'                => 'act=edit'
            ,   'icon'                => 'header.svg'
            ]
        ,   'copy' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['copy']
            ,   'href'                => 'act=copy'
            ,   'icon'                => 'copy.svg'
            ]
        ,   'delete' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['delete']
            ,   'href'                => 'act=delete'
            ,   'icon'                => 'delete.svg'
            ,   'attributes'          => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"'
            ]
        ,   'show' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_categories']['show']
            ,   'href'                => 'act=show'
            ,   'icon'                => 'show.svg'
            ]
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
        ,   'sql'           => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
        ]
    ,   'map_pin' => [
            'inputType'     => 'fileTree'
        ,   'eval'          => ['filesOnly'=>true, 'extensions'=>Config::get('validImageTypes'), 'fieldType'=>'radio']
        ,   'sql'           => "binary(16) NULL"
        ]
    ]
];
