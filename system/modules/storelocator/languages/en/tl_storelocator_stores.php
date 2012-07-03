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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_storelocator_stores']['name']   		= array('Name', 'Name of the store.');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['email']   		= array('E-Mail', 'E-Mail Address');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['url']   			= array('Website', 'Enter the URL of the stores website');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['phone']   		= array('Phone', 'Telephone number');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['fax']   			= array('Fax', 'Fax number');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['street']   		= array('Street', 'Name of the street plus house number');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['postal']   		= array('Zip code', 'Zip code');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['city']   		= array('City', 'Name of the city');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['country']		= array('Country', 'Please choose a country');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['opening_times']	= array('Öffnungszeiten', '');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_weekday']	= array('Opening times', '');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_from']		= array('from', '');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_to']		= array('to', '');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['longitude']		= array('Longitude', 'To be filled in automatically...');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['latitude']		= array('Latitude', 'To be filled in automatically...');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['map']			= array('Map');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['geo_explain']	= array('The geographic coordinates are needed for the search to be working correctly. Later on the user will be able to search for stores near his given location. Both of these fields will be filled in automatically but also can be fixed manually.');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_storelocator_stores']['common_legend']  = 'Common';
$GLOBALS['TL_LANG']['tl_storelocator_stores']['adress_legend']  = 'Address';
$GLOBALS['TL_LANG']['tl_storelocator_stores']['geo_legend'] 	= 'Coordinates';
$GLOBALS['TL_LANG']['tl_storelocator_stores']['times_legend'] 	= 'Opening times';
 

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_storelocator_stores']['new']    		= array('New store', 'Add a new store');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['show']   		= array('Details', 'Details about store with ID %s');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['edit']   		= array('Edit store', 'Edit store with ID %s');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['copy']   		= array('Copy store', 'Copy store with ID %s');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['delete'] 		= array('Delete store', 'Delete store with ID %s');
$GLOBALS['TL_LANG']['tl_storelocator_stores']['importStores'] 	= array('CSV import', 'Import stores from a CSV file');
?>