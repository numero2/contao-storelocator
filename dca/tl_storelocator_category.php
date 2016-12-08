<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package   StoreLocator
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright 2016 numero2 - Agentur für Internetdienstleistungen
 */


/**
* Table tl_storelocator_category
*/
$GLOBALS['TL_DCA']['tl_storelocator_category'] = array(

	'config' => array(
		'dataContainer'               => 'Table'
	,	'ctable'                      => array('tl_storelocator_stores')
	,	'switchToEdit'                => true
	,	'onload_callback' => array( array('numero2\StoreLocator\StoreLocator','showGoogleKeysMissingMessage') )
    ,   'sql' => array (
            'keys' => array (
                'id' => 'primary'
            )
        )
	)
,	'list' => array(
		'sorting' => array(
			'mode'                    => 1
		,	'fields'                  => array('title')
		,	'flag'                    => 1
		,	'panelLayout'             => 'filter;search,limit'
		)
	,	'label' => array(
			'fields'                  => array('title')
		,	'format'                  => '%s'
		)
	,	'global_operations' => array(
			'all' => array(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all']
			,	'href'                => 'act=select'
			,	'class'               => 'header_edit_all'
			,	'attributes'          => 'onclick="Backend.getScrollOffset();"'
			)
		)
	,	'operations' => array(
			'edit' => array(
				'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_category']['edit']
			,	'href'                => 'table=tl_storelocator_stores'
			,	'icon'                => 'edit.gif'
			)
		,	'copy' => array(
				'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_category']['copy']
			,	'href'                => 'act=copy'
			,	'icon'                => 'copy.gif'
			)
		,	'delete' => array(
				'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_category']['delete']
			,	'href'                => 'act=delete'
			,	'icon'                => 'delete.gif'
			,	'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			)
		,	'show' => array(
				'label'               => &$GLOBALS['TL_LANG']['tl_storelocator_category']['show']
			,	'href'                => 'act=show'
			,	'icon'                => 'show.gif'
			)
		)
	)
,	'palettes' => array(
		'default'                     => '{title_legend},title'
	)
,	'fields' => array(
        'id' => array(
            'sql'           => "int(10) unsigned NOT NULL auto_increment"
        )
    ,   'tstamp' => array(
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        )
    ,   'title' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_category']['title']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64)
        ,   'sql'                     => "varchar(64) NOT NULL default ''"
		)
	)
);
