<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  numero2 - Agentur für Internetdienstleistungen <www.numero2.de>
 * @author     Benny Born <benny.born@numero2.de>
 * @package    storelocator
 * @license    LGPL
 * @filesource
 */


/**
* Table tl_storelocator_category
*/
$GLOBALS['TL_DCA']['tl_storelocator_category'] = array(

	'config' => array(
		'dataContainer'               => 'Table'
	,	'ctable'                      => array('tl_storelocator_stores')
	,	'switchToEdit'                => true
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
		'title' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_storelocator_category']['title']
		,	'inputType'               => 'text'
		,	'search'                  => true
		,	'eval'                    => array('mandatory'=>true, 'maxlength'=>64)
		)
	)
);

?>