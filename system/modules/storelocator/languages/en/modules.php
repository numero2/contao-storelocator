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
 * Legends
 */
$GLOBALS['TL_LANG']['tl_module']['slmap_legend'] = 'Google Maps';


/**
 * Back end modules
 */
$GLOBALS['TL_LANG']['MOD']['storelocator'] = array('Stores', 'Manage stores and search them using geodata');


/**
 * Front end modules
 */
$GLOBALS['TL_LANG']['FMD']['storelocator'] = array('Stores', '');
$GLOBALS['TL_LANG']['FMD']['storelocator_search'] = array('Storesearch', 'adds a search mask');
$GLOBALS['TL_LANG']['FMD']['storelocator_list'] = array('Storelist', 'lists all available stores');
$GLOBALS['TL_LANG']['FMD']['storelocator_details'] = array('Storedetails', 'shows details for the chosen store');

$GLOBALS['TL_LANG']['tl_module']['storelocator_search_country'] = array('Default country', 'Which country should be used as default if user did not select any?');
$GLOBALS['TL_LANG']['tl_module']['storelocator_show_full_country_names'] = array('Show full country names?', 'Should country names be shortened?');
$GLOBALS['TL_LANG']['tl_module']['storelocator_search_tpl'] = array('Template', 'Which template should be used to display the search mask.');

$GLOBALS['TL_LANG']['tl_module']['storelocator_list_tpl'] = array('Template', 'Chosse which template should be used to list the stores');
$GLOBALS['TL_LANG']['tl_module']['storelocator_list_categories'] = array('Categories', 'From which category should the stores be displayed');
$GLOBALS['TL_LANG']['tl_module']['storelocator_allow_empty_search'] = array('Allow empty search?', 'Should the user be able to submit an empty search to see all results?');
$GLOBALS['TL_LANG']['tl_module']['storelocator_list_limit'] = array('Number of results', 'How many results should be shown?');

$GLOBALS['TL_LANG']['tl_module']['storelocator_details_tpl'] = array('Template', 'Chosse which template should be used to show details of the store');

$GLOBALS['TL_LANG']['tl_module']['storelocator_details_maptype'] = array('Type', 'Which type of Google Map should be shown?');
$GLOBALS['TL_LANG']['tl_module']['storelocator_details_maptypes'] = array('static', 'dynamic');
?>